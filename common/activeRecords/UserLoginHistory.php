<?php

namespace common\activeRecords;

use Yii;

/**
 * This is the model class for table "user_login_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $client_id
 * @property integer $device_id
 * @property string $app_version_name
 * @property string $ip
 * @property string $created_at
 */
class UserLoginHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_login_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'client_id'], 'required'],
            [['user_id', 'device_id'], 'integer'],
            [['created_at'], 'safe'],
            [['client_id', 'app_version_name', 'ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'client_id' => 'Client ID',
            'device_id' => 'Device ID',
            'app_version_name' => 'App Version Name',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\UserLoginHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\UserLoginHistoryQuery(get_called_class());
    }
}
