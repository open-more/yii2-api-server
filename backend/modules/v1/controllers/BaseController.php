<?php

namespace backend\modules\v1\controllers;

use common\exceptions\rbac\PermissionDennyException;
use Yii;
use yii\base\InlineAction;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class BaseController extends \yii\rest\Controller
{
    /** @var bool 默认不需要授权访问 */
    private $_rbacEnable = true;

    /** @var  string */
    protected $token;

    public function init()
    {
        parent::init();

        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $this->token = $matches[1];
        }

//        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        Yii::$app->response->charset = 'utf-8';
    }

    /**
     * 配置API认证方法
     * 使用HttpBearerAuth认证,类似于OAuth2
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // 设置cors,再设置authenticator
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        if($this->_rbacEnable){
            $behaviors['authenticator'] = [
                'class'  => HttpBearerAuth::class,
                'except' => ArrayHelper::merge(['options'], $this->optional()),
                'only' => $this->only(),
            ];
        }

        return $behaviors;
    }

    public function beforeAction($action)
    {
        // 先执行父类beforAction
        $result = parent::beforeAction($action);
        // 不需要授权访问,直接返回
        if(!$this->isEnableRbac()){
            return $result;
        }
        if($action instanceof InlineAction){
            $actionName = $this->action->id;
            $controller = $this->action->controller->id;
            $module = $this->action->controller->module->id;
            // 具体Action的权限验证
            $permissionName = "{$module}/{$controller}/{$actionName}";
            // 控制器内所有Action通配符验证
            $wildcardPerm = "{$module}/{$controller}/*";

            // 不需要访问检查的action,排除在外
            if($actionName === 'options' ||
                in_array('*', $this->rbacCheckExcept()) ||
                in_array($actionName, $this->rbacCheckExcept())){
                return $result;
            }
            if(Yii::$app->user->can($wildcardPerm) || Yii::$app->user->can($permissionName)){
                return $result;
            }else{
                throw new PermissionDennyException();
            }
        }

    }

    /**
     * 默认的Options接口,返回OK
     * @return string
     */
    public function actionOptions()
    {
        return 'OK';
    }

    /**
     * 不需要进行RBAC限制的接口
     * @return array
     */
    public function rbacCheckExcept()
    {
        return [];
    }

    /**
     * 关闭当前Controller的授权
     */
    public function disableRbac(){
        $this->_rbacEnable = false;
    }

    /**
     * 是否开启了授权
     * @return bool
     */
    public function isEnableRbac(){
        return $this->_rbacEnable;
    }

    /**
     * 需要token权限的接口
     * @return array
     */
    protected function only()
    {
        return [];
    }

    /**
     * 可不需要token权限的接口
     * @return array
     */
    protected function optional()
    {
        return [];
    }
}