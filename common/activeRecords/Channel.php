<?php

namespace common\activeRecords;

use Yii;

/**
 * This is the model class for table "channel".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $banner
 * @property string $icon
 * @property string $promotion_image
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Channel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'description', 'banner', 'icon', 'promotion_image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'banner' => 'Banner',
            'icon' => 'Icon',
            'promotion_image' => 'Promotion Image',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\ChannelQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\ChannelQuery(get_called_class());
    }
}
