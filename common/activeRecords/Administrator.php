<?php

namespace common\activeRecords;

use common\components\JwtAuth;
use common\exceptions\common\UnimplementException;
use common\traits\model\ModelTrait;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "administrator".
 *
 * @property integer $id
 * @property string $account
 * @property string $password
 * @property string $user_name
 * @property integer $type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Administrator extends \yii\db\ActiveRecord implements IdentityInterface
{
    const TYPE_DEFAULT = 0;
    const TYPE_SUPER = 10;


    const TYPE_NAME = [
        self::TYPE_DEFAULT => '默认',
        self::TYPE_SUPER => '超级管理员'
    ];

    const STATUS_DELETED = -1;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 20;

    const STATUS_NAME = [
        self::STATUS_DELETED => '已经删除',
        self::STATUS_ACTIVE => '可用',
        self::STATUS_INACTIVE => '不可用'
    ];

    use ModelTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'administrator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['account', 'password', 'user_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => '登录账号,必须是邮件',
            'password' => '密码',
            'user_name' => '显示用户名',
            'type' => '10为超级管理员',
            'status' => '题图',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\AdministratorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\AdministratorQuery(get_called_class());
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
        /** @var JwtAuth $auth */
        $auth = Yii::$app->auth;
        $uid = $auth->getIdFromToken($tokenString);
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
