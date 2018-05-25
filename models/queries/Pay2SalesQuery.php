<?php

namespace tecsin\pay2\models\queries;

/**
 * This is the ActiveQuery class for [[\tecsin\pay2\models\Pay2Sales]].
 *
 * @see \tecsin\pay2\models\Pay2Sales
 */
class Pay2SalesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \tecsin\pay2\models\Pay2Sales[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \tecsin\pay2\models\Pay2Sales|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
