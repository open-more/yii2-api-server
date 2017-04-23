<?php

namespace common\components;

use common\exceptions\Code;
use common\exceptions\data\ValidateFailedException;
use common\exceptions\Exception;
use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

class JwtAuth extends Component
{


    const SCOPE_APP = 'app';
    const SCOPE_BACKEND = 'backend';
    const SCOPE_FRONTEND = 'frontend';

    public $key = 'www.openmore.org';
    /**
     * token失效时间
     * @var int
     */
    public $expire = 14 * 24 * 3600;

    /**
     * token过期时间
     * @var int
     */
    public $refresh = 7200;

    /**
     * 发行者
     * @var string
     */
    public $issuer = 'www.openmore.org';

    /**
     * 是否开启互踢
     * @var bool
     */
    public $enableKickOff = true;


    /**
     * 每分钟允许请求最大数
     * @var int
     */
    public $max_request_count = 60;

    public $return_scope = false;
    public $return_user_id = false;
    public $return_expire  = true;

    /**
     * 获得AccessToken
     * @param int $id
     * @param string $scope
     * @return array
     * @throws Exception
     */
    public function createAccessToken($id = 0, $scope = self::SCOPE_APP){
        switch ($scope){
            case self::SCOPE_APP:
            case self::SCOPE_BACKEND:
            case self::SCOPE_FRONTEND:
                break;
            default:
                throw new Exception(Code::TOKEN_SCOPE_ERROR);
        }

        $now = time();
        $jti = "{$now}{$this->getNonce(4)}";
        $nonce = $this->getNonce(6);
        $exp = $now + $this->expire;       // 2周失效
        $singer = new Sha256();
        $token = (new Builder())
            ->setIssuer($this->issuer)// Configures the issuer (iss claim)
            ->setId($jti, true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt($now)// Configures the time that the token was issue (iat claim)
            ->setExpiration($exp)// Configures the expiration time of the token (nbf claim)
            ->set('scope', $scope)// Configures a new claim, called "uid"
            ->set('nonce', $nonce);

        if($id > 0){
            $token->set('user_id', $id);
        }

        $token->sign($singer, $this->key);

        // 保存本次授权信息,用于重放检查与每分钟访问次数限制检查
        if(isset(Yii::$app->cache)){
            $value = [
                'nonce' => $nonce,              // 随机数
                'minute' => (int)($now / 60),   // 当前分钟
                'count' => 0,                   // 接口访问次数
            ];

            $key = $jti;
            if($id > 0){
                // 登录用户使用session_[user]作为保存数据key
                $key = "session_{$id}";
            }
            Yii::$app->cache->set($key, $value, $exp - time());
        }
        $returnBody = [
            'token' => $token->getToken()->__toString(),
            'expire_in' => ($now + $this->refresh)
        ];
        if($this->return_expire){
            $returnBody = ArrayHelper::merge(['expire_in' => ($now + $this->refresh)], $returnBody);
        }
        if($this->return_scope){
            $returnBody = ArrayHelper::merge(['scope' => $scope], $returnBody);
        }
        if($this->return_user_id){
            $returnBody = ArrayHelper::merge(['user_id' => $id], $returnBody);
        }
        return $returnBody;
    }

    /**
     * 拉黑token
     * @param $token
     * @return bool
     * @throws Exception
     * @throws ValidateFailedException
     */
    public function banToken($token){
        if(isset(Yii::$app->cache)){
            /** @var Token $tokenObj */
            try{
                $parser = new Parser();
                /** @var Token $newTokenObj */
                $tokenObj = $parser->parse($token);
            }catch (InvalidArgumentException $e){
                // 解包失败
                throw new Exception(Code::TOKEN_PARSE_ERROR);
            }

            // 如果登录用户,通过session_[user_id]获得保存数据,否则通过jwt来获得
            $key = $tokenObj->getClaim('jti');
            if($tokenObj->hasClaim('user_id')){
                $key = "session_{$tokenObj->hasClaim('user_id')}";
            }
            Yii::$app->cache->delete($key);
            return true;
        }else{
            throw new Exception('没有配置cache,无法使用该功能');
        }
    }


    /**
     * 从Token里获得Id
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public function getIdFromToken($token)
    {
        /** @var Token $tokenObj */
        $tokenObj = $this->parseToken($token);
        return $tokenObj->getClaim('user_id', 0);
    }


    /**
     * 从Token里获得Scope
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public function getScopeFromToken($token)
    {
        /** @var Token $tokenObj */
        $tokenObj = $this->parseToken($token);
        return $tokenObj->getClaim('scope', null);
    }

    /**
     * 从Token里获得访客ID(jti)
     * @param $token
     * @return mixed
     * @throws Exception
     */
    public function getGuestIdFromToken($token){
        /** @var Token $tokenObj */
        $tokenObj = $this->parseToken($token);
        return $tokenObj->getClaim('jti', 0);
    }

    /**
     * 检查token并返回更新的token
     * @deprecated 考虑到性能,不使用换发token
     * @param $token
     * @param $scope
     * @param int $user_id
     * @return array
     * @throws Exception
     */
    public function checkAndRefreshToken($token, $scope, $user_id = 0){
        $tokenObj = $this->parseToken($token, $scope, $user_id);
//        var_dump($tokenObj->getClaims());

        // 重新设置6位随机数
        $newNonce = $this->getNonce(6);
        // 更新token,nonce及iat
        return $this->refresh($tokenObj->getClaim('iss'), $tokenObj->getClaim('jti'), $tokenObj->getClaim('exp'), $tokenObj->getClaim('scope'), $newNonce, $user_id);
    }

    /**
     * 解析并检查token
     * @param $token
     * @param null $scope
     * @param int $user_id
     * @return Token
     * @throws Exception
     */
    private function parseToken($token, $scope = null, $user_id = 0){
        try{
            $parser = new Parser();
            /** @var Token $newTokenObj */
            $tokenObj = $parser->parse($token);
        }catch (InvalidArgumentException $e){
            // 解包失败
            throw new Exception(Code::TOKEN_PARSE_ERROR);
        }

        // 1.检查签名
        if(!$tokenObj->verify(new Sha256(), $this->key)){
            throw new Exception(Code::TOKEN_SIGN_ERROR);
        }

        // 2.检查scope
        if($scope){
            if(!$tokenObj->hasClaim('scope') ||  $tokenObj->getClaim('scope') !== $scope) {
                throw new Exception(Code::TOKEN_SCOPE_ERROR);
            }
        }

        // 3.检查user_id,如果存在,需要和当前用户id比较
        if($user_id){
            if(!$tokenObj->hasClaim('user_id') || $tokenObj->getClaim('user_id') !== $user_id) {
                throw new Exception(Code::TOKEN_USER_REFUSE);
            }
        }

        // 4.判断是否失效
        if($tokenObj->isExpired()){
            throw new Exception(Code::TOKEN_EXPIRED);
        }

        // 4.判断是否过期,上次发签名时间超时
        if($this->refresh > 0){
            if(!$tokenObj->hasClaim('iat') ||  time() - $tokenObj->getClaim('iat') > $this->refresh){
                throw new Exception(Code::TOKEN_NEED_REFRESH);
            }
        }

        // 5.重放检查与每分钟访问次数限制检查,需要使用redis
        if(isset(Yii::$app->cache)){
            // 如果登录用户,通过session_[user_id]获得保存数据,否则通过jwt来获得
            $key = $tokenObj->getClaim('jti');
            if($tokenObj->hasClaim('user_id')){
                $key = "session_{$tokenObj->getClaim('user_id')}";
            }
            $lastTokenObj = Yii::$app->cache->get($key);
//            var_dump($lastTokenObj);die();
            if(!$lastTokenObj){
                // 如果已经过期,说明是自然失效
                if($tokenObj->isExpired()){
                    throw new Exception(Code::TOKEN_EXPIRED);
                }
            }
            // 如果redis里的Nonce和请求token的nonce不一致,该用户ID已经重新登录,并创建新token,返回被踢
            if($this->enableKickOff && $lastTokenObj['nonce'] !== $tokenObj->getClaim('nonce')){
                throw new Exception(Code::TOKEN_KICK_OFF);
            }
            $count = 0;
            // 当前分钟
            $minute = (int)(time() / 60);
            // 同一分钟内的请求
            if($minute === $lastTokenObj['minute']){
                // 一分钟内请求次数超过60次
                if($this->max_request_count > 0 && $lastTokenObj['count'] >= $this->max_request_count){
                    throw new Exception(Code::TOKEN_TOO_MANY_REQ);
                }
                $count = $lastTokenObj['count'] + 1;
            }
            $value = [
                'nonce' => $tokenObj->getClaim('nonce'),
                'minute' => $minute,
                'count' => $count,
            ];
            Yii::$app->cache->set($key, $value, $tokenObj->getClaim('exp') - time());
        }

        return $tokenObj;
    }

    /**
     * 刷新指定的token
     * @param $token
     * @return array
     * @throws Exception
     */
    public function refreshToken($token){
        try{
            $parser = new Parser();
            /** @var Token $newTokenObj */
            $tokenObj = $parser->parse($token);
        }catch (InvalidParamException $e){
            // 解包失败
            throw new Exception(Code::TOKEN_PARSE_ERROR);
        }

        // 1.检查签名
        if(!$tokenObj->verify(new Sha256(), $this->key)){
            throw new Exception(Code::TOKEN_SIGN_ERROR);
        }

        // 2.判断是否失效
        if($tokenObj->isExpired()){
            throw new Exception(Code::TOKEN_EXPIRED);
        }
        $nonce = $this->getNonce(6);
        $uid = $tokenObj->hasClaim('user_id') ? $tokenObj->getClaim('user_id') : 0;
        return $this->refresh($tokenObj->getClaim('iss'), $tokenObj->getClaim('jti'), $tokenObj->getClaim('exp'), $tokenObj->getClaim('scope'), $nonce, $uid);
    }

    /**
     * 根据参数,更新token,同时保存到redis里
     * @param $iss
     * @param $jti
     * @param $exp
     * @param $scope
     * @param $nonce
     * @param int $user_id
     * @return array
     */
    private function refresh($iss, $jti, $exp, $scope, $nonce, $user_id = 0) : array{
        $singer = new Sha256();
        $newToken = (new Builder())
            ->setIssuer($iss)// Configures the issuer (iss claim)
            ->setId($jti, true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setExpiration($exp)// Configures the expiration time of the token (nbf claim)
            ->set('scope', $scope)// Configures a new claim, called "uid"
            ->set('nonce', $nonce);

        if($user_id){
            $newToken->set('user_id', $user_id);
        }

        $newToken->sign($singer, $this->key);

        if(isset(Yii::$app->cache)){
            // 如果登录用户,通过session_[user_id]获得保存数据,否则通过jwt来获得
            $key = $jti;
            if($user_id){
                $key = "session_{$user_id}";
            }
            $lastTokenObj = Yii::$app->cache->get($key);
            $count = 0;
            $minute = (int)(time() / 60);
            if($lastTokenObj){
                // 同一分钟内的请求
                if($lastTokenObj['minute'] === $minute){
                    $count = $lastTokenObj['count'] + 1;
                }
            }
            $value = [
                'nonce' => $nonce,
                'minute' => $minute,
                'count' => $count,
            ];
            Yii::$app->cache->set($key, $value, $exp - time());
        }

//        var_dump($newToken->getToken()->getClaims());

        $returnBody = [
            'token' => $newToken->getToken()->__toString(),
        ];
        if($this->return_expire){
            $returnBody = ArrayHelper::merge(['expire_in' => (time() + $this->refresh)], $returnBody);
        }
        if($this->return_scope){
            $returnBody = ArrayHelper::merge(['scope' => $scope], $returnBody);
        }
        if($this->return_user_id){
            $returnBody = ArrayHelper::merge(['user_id' => $user_id], $returnBody);
        }
        return $returnBody;
    }

    /**
     * 生成指定长度的随机字母数字组合
     * @param $len
     * @param null $chars
     * @return string
     */
    public function getNonce($len, $chars=null)
    {
        if (is_null($chars)){
            // 数字多一些,没有字母O,防止和数字0有歧义
            $chars = "23456789ABCDE23456789FGHJ23456789KLMN23456789PQRST23456789UVWX23456789YZ23456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

}