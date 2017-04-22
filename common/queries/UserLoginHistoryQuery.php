<?php

namespace common\queries;

/**
 * This is the ActiveQuery class for [[\common\activeRecords\UserLoginHistory]].
 *
 * @see \common\activeRecords\UserLoginHistory
 */
class UserLoginHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\activeRecords\UserLoginHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\activeRecords\UserLoginHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
