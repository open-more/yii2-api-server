<?php

namespace common\activeRecords;

use common\components\JwtAuth;
use common\exceptions\common\UnimplementException;
use common\traits\model\ModelTrait;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $nickname
 * @property string $avatar
 * @property string $occupation
 * @property string $birthday
 * @property integer $gender
 * @property string $location
 * @property integer $is_test
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $logined_at
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    use ModelTrait;

    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 20;
    const STATUS_ACTIVE = 10;


    const STATUS_NAME = [
        self::STATUS_DELETED => '已经删除',
        self::STATUS_ACTIVE => '可用',
        self::STATUS_INACTIVE => '不可用'
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birthday', 'created_at', 'updated_at', 'logined_at'], 'safe'],
            [['gender', 'is_test', 'status'], 'integer'],
            [['mobile', 'nickname'], 'string', 'max' => 32],
            [['avatar'], 'string', 'max' => 1024],
            [['occupation', 'location'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号',
            'nickname' => '昵称',
            'avatar' => '头像',
            'occupation' => '工作',
            'birthday' => '生日',
            'gender' => '性别 0:未知 1:男 2:女',
            'location' => '地区',
            'is_test' => '是否测试用户 0:否 1:是',
            'status' => '状态 10:激活用户 20:禁止用户',
            'created_at' => '创建时间即注册时间',
            'updated_at' => '记录更新时间',
            'logined_at' => '上次登录时间',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\UserQuery(get_called_class());
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        if($id){
            return self::findOne($id);
        }
        return null;
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (is_array($token)) {
            $tokenString = $token['token'] ?? null;
        } else {
            $tokenString = $token;
        }
        /** @var JwtAuth $authBiz */
        $auth = Yii::$app->auth;
        $uid = $auth->getUserIdFromToken($tokenString);
        return static::findIdentity($uid);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     * @throws UnimplementException
     */
    public function getAuthKey()
    {
        throw new UnimplementException();
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     * @param string $authKey
     * @throws UnimplementException
     */
    public function validateAuthKey($authKey)
    {
        throw new UnimplementException();
    }
}
