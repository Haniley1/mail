<?php

use yii\db\Migration;

class m220803_052250_create_message_entry_reaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('message_entry_reaction', [
            'id' => $this->primaryKey(),
            'message_entry_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string(20)->notNull(),
            'created_at' => $this->dateTime()->notNull()
        ]);

        $this->createIndex(
            'IDX_message_entry_reaction_message_entry_id_user_id_type',
            'message_entry_reaction',
            ['message_entry_id', 'user_id', 'type'],
            true
        );

        $this->createIndex(
            'IDX_message_entry_reaction_message_entry_id',
            'message_entry_reaction',
            ['message_entry_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('message_entry_reaction');
    }
}
