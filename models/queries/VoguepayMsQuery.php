<?php

namespace tecsin\pay2\models\queries;

/**
 * This is the ActiveQuery class for [[VoguepayMs]].
 *
 * @see VoguepayMs
 */
class VoguepayMsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return VoguepayMs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return VoguepayMs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
