<?php

namespace common\queries;

/**
 * This is the ActiveQuery class for [[\common\activeRecords\Tag]].
 *
 * @see \common\activeRecords\Tag
 */
class TagQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\activeRecords\Tag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\activeRecords\Tag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
