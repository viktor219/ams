<?php

use yii\db\Migration;

class m160305_081657_create_user_log_tracking extends Migration
{
    public function up()
    {
        $this->createTable('user_log_tracking', [
            'id' => $this->primaryKey()
        ]);
    }

    public function down()
    {
        $this->dropTable('user_log_tracking');
    }
}
