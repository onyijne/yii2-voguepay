<?php

namespace tecsin\pay2\models\queries;

/**
 * This is the ActiveQuery class for [[CommandApiHistory]].
 *
 * @see CommandApiHistory
 */
class CommandapiHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CommandApiHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CommandApiHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
    
    public function getTask($type)
    {
        
    }
}
