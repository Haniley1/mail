<?php

use yii\db\Migration;

/**
 * Class m220711_092850_add_reply_column
 */
class m220711_092850_add_reply_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('message_entry', 'message_entry_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('message_entry', 'message_entry_id');
    }
}
