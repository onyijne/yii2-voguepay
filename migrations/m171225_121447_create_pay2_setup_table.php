<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pay2_setup}}`.
 */
class m171225_121447_create_pay2_setup_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%pay2_setup}}', [
            'id' => $this->primaryKey(),
            'merchant_id' => $this->string()->notNull(),
            'success_url' => $this->string()->notNull(),
            'failure_url' => $this->string()->notNull(),
            'api_key' => $this->string()->notNull(),
            'voguepay_email' => $this->string()->notNull(),
            'bank_name' => $this->string(),
            'account_name' => $this->string(),
            'account_number' => $this->string(),
            'account_type' => $this->string(),
            'payment_instruction' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%pay2_setup}}');
    }
}
