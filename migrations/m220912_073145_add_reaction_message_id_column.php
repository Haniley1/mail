<?php

use yii\db\Migration;

/**
 * Class m220912_073145_add_reaction_message_id_column
 */
class m220912_073145_add_reaction_message_id_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('message_entry_reaction', 'message_id', $this->integer());

        $this->createIndex(
            'IDX_message_entry_reaction_message_id_user_id',
            'message_entry_reaction',
            ['message_id', 'user_id']
        );
    }

    public function safeDown()
    {
        $this->dropColumn('message_entry_reaction', 'message_id');
    }
}
