<?php

namespace common\activeRecords;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $sub_title
 * @property string $content
 * @property string $image_url
 * @property string $go_url
 * @property integer $type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'sub_title', 'content', 'go_url'], 'string', 'max' => 255],
            [['image_url'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'sub_title' => '副标题',
            'content' => '内容',
            'image_url' => '新闻图URL',
            'go_url' => '新闻跳转链接',
            'type' => 'Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\NewsQuery(get_called_class());
    }
}
