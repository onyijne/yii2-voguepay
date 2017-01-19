<?php

namespace tecsin\pay2\models;

use Yii;

/**
 * PLEASE CREATE THIS TABLE IF YOU WANT TO USE IT
 * 
 * This is the model class for table "{{%sales}}".
 *
 * @property integer $id
 * @property string $ref
 * @property string $remark
 * @property double $credit
 * @property string $matureDate
 * @property string $salesDate
 * @property string $memo
 * @property string $items
 * @property double $total
 * @property double $total_paid
 * @property string $gateway
 */
class Sales extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sales}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ref', 'items', 'total'], 'required'],
            [['memo', 'items'], 'string'],
            [['total', 'total_paid', 'credit'], 'double'],
            [['salesDate', 'matureDate'], 'safe'],
            [['ref', 'remark', 'gateway'], 'string', 'max' => 250],
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
            'remark' => Yii::t('app', 'Remark'),
            'memo' => Yii::t('app', 'Memo'),
            'matureDate' => Yii::t('app', 'Mature Date'),
            'credit' => Yii::t('app', 'Credit'),
            'items' => Yii::t('app', 'Items'),
            'total' => Yii::t('app', 'Total'),
            'total_paid' => Yii::t('app', 'Total Paid'),
            'gateway' => Yii::t('app', 'Gateway'),
        ];
    }

}