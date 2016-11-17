<?php

use yii\db\Migration;

class m160229_194132_add_customer_fields extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%customers}}';
	}
	
    public function safeUp()
    {
		$this->before();
		
		$this->addColumn($this->tableName, 'customer_administrator', 'integer NOT NULL DEFAULT 0');		
		
		$this->addColumn($this->tableName, 'parent_id', 'integer NULL');		
    }

    public function safeDown()
    {
        echo "m160229_194132_add_customer_fields cannot be reverted.\n";

        return false;
    }
}
