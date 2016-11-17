<?php

use yii\db\Migration;

class m160226_173754_create_table_itemsorder extends Migration
{
    public function up()
    {
        /*$this->createTable('table_itemsorder', [
            'id' => $this->primaryKey()
        ]);*/
    }

    public function down()
    {
        $this->dropTable('table_itemsorder');
    }
}
