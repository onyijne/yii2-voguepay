<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pay2_sales}}`.
 */
class m171224_121356_create_pay2_sales_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%pay2_sales}}', [
            'id' => $this->primaryKey(),
            'ref' => $this->string()->comment('Transaction Reference'),
            'remark' => $this->string()->comment('Transction Remark'),
            'received_amount' => $this->string()->comment('Amount Credited To Merchant'),
            'mature_date' => $this->string()->comment('Fund Mature Date'),
            'transaction_date' => $this->string(),
            'memo' => $this->text(),
            'total' => $this->string()->comment('Total Cost'),
            'total_paid' => $this->string()->comment('Total Paid'),
            'extra_charges' => $this->string(),
            'gateway' => $this->string(),
            'user_id' => $this->string(),
            'referrer' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%pay2_sales}}');
    }
}
