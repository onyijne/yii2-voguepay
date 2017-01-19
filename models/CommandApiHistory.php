<?php

namespace tecsin\pay2\models;

use Yii;
use tecsin\pay2\models\queries\CommandapiHistoryQuery;

/**
 * This is the model class for table "{{%commandapi_history}}".
 *
 * @property integer $id
 * @property string $ref
 * @property string $task
 * @property string $type
 * @property string $status
 */
class CommandApiHistory extends \yii\db\ActiveRecord
{
    public $result = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%commandapi_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref', 'task', 'type', 'status'], 'string', 'max' => 255],
            [['ref', 'task', 'type', 'status'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ref' => Yii::t('app', 'Ref'),
            'task' => Yii::t('app', 'Task'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @inheritdoc
     * @return CommandapiHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommandapiHistoryQuery(get_called_class());
    }
   
}
