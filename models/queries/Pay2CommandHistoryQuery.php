<?php

namespace tecsin\pay2\models\queries;

/**
 * This is the ActiveQuery class for [[Pay2CommandHistory ]].
 *
 * @see Pay2CommandHistory
 */
class Pay2CommandHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Pay2CommandHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Pay2CommandHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function getTask($type)
    {
        
    }
}
