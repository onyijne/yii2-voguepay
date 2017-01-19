<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%voguepay_ms}}`.
 */
class m170114_232715_create_voguepay_ms_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%voguepay_ms}}', [
            'msID' => $this->primaryKey(),
            'aaaMerchantId' => $this->string()->comment('Merchant ID')->notNull(),
            'mmmMemo' => $this->text()->comment('Memo')->notNull(),
            'tttTotalCost' => $this->string()->comment('Total Cost')->notNull(),
            'rrrMerchantRef' => $this->string()->comment('Transaction Reference'),
            'cccRecurrentBillingStatus' => $this->boolean()->defaultValue(false),
            'iiiRecurrenceInterval' => $this->integer()->comment('No of days between each recurrent billing if recurrent is set to true.'),
            'nnnNotificationUrl' => $this->string()->comment('Notification Url'),
            'sssSuccessUrl' => $this->string()->comment('Success Url'),
            'fffFailUrl' => $this->string()->comment('Fail Url'),
            'dddDeveloperCode' => $this->string()->defaultValue('573cedec3bee0'),
            'cccCurrencyCode' => $this->string()->comment('Currency Code'),
            'msResponse' => $this->text()->comment('Response Link'),
            'msExpireAt' => $this->string(),
            'siteProductId' => $this->string()->defaultValue('0'),
            'msStatus' => $this->string()->defaultValue('Pending'),
        ]);
        
        $this->createIndex('IdxMref', '{{%voguepay_ms}}', 'rrrMerchantRef', true);
        $this->createIndex('Idxmulti', '{{%voguepay_ms}}', ['aaaMerchantId', 'siteProductId']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%voguepay_ms}}');
    }
}
