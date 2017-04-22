<?php

namespace common\activeRecords;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property string $device_token
 * @property string $app_version_name
 * @property string $channel_code
 * @property string $connection_type
 * @property string $cpu_info
 * @property string $model_name
 * @property string $location
 * @property string $rom_info
 * @property string $vendor
 * @property string $wifi_mac
 * @property string $host_name
 * @property string $push_token
 * @property string $created_at
 * @property string $updated_at
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['device_token', 'app_version_name', 'channel_code', 'connection_type', 'cpu_info', 'model_name', 'location', 'rom_info', 'vendor', 'wifi_mac', 'host_name', 'push_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_token' => '自己的设备token',
            'app_version_name' => '当前App的版本名',
            'channel_code' => '渠道名',
            'connection_type' => '联网方式',
            'cpu_info' => '设备CPU',
            'model_name' => '型号',
            'location' => '设备位置',
            'rom_info' => '设备系统信息',
            'vendor' => '设备厂商',
            'wifi_mac' => 'WIFI地址可为空',
            'host_name' => '主机名',
            'push_token' => '第三方推送token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\DeviceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\DeviceQuery(get_called_class());
    }
}
