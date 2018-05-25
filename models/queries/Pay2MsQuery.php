<?php

namespace tecsin\pay2\models\queries;

/**
 * This is the ActiveQuery class for [[Pay2Ms]].
 *
 * @see Pay2Ms
 */
class Pay2MsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Pay2Ms[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Pay2Ms|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
