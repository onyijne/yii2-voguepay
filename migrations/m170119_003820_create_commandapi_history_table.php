<?php

use yii\db\Migration;

/**
 * Handles the creation of table `pay2_command_history`.
 */
class m170119_003820_create_commandapi_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%pay2_command_history}}', [
            'id' => $this->primaryKey(),
            'ref' => $this->string(),
            'task' => $this->string(),
            'type' => $this->string(),
            'status' => $this->string(),
        ]);
        $this->createIndex('IdxRefTask', '{{%pay2_command_history}}', ['ref', 'task']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%pay2_command_history}}');
    }
}
