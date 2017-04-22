<?php

namespace common\activeRecords;

use Yii;

/**
 * This is the model class for table "tag_user_r".
 *
 * @property integer $tag_id
 * @property integer $user_id
 * @property string $created_at
 */
class TagUserR extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_user_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_id', 'user_id'], 'required'],
            [['tag_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\queries\TagUserRQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\queries\TagUserRQuery(get_called_class());
    }
}
