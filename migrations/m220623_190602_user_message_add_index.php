<?php

use yii\db\Migration;

class m220623_190602_user_message_add_index extends Migration
{
    public function up()
    {
        $this->createIndex('IDX_user_message_message_id_user_id', 'user_message', ['message_id', 'user_id'], true);
    }

    public function down()
    {
        $this->dropIndex('IDX_user_message_message_id_user_id', 'user_message');
    }
}
