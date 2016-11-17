<?php

use yii\db\Migration;

class m160229_200404_add_name_fields extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%customers}}';
	}
	
    public function safeUp()
    {
		$this->before();
		
		$this->addColumn($this->tableName, 'firstname', 'VARCHAR(100) NULL');		
		
		$this->addColumn($this->tableName, 'lastname', 'VARCHAR(100) NULL');		
    }

    public function safeDown()
    {
        echo "m160229_200404_add_name_fields cannot be reverted.\n";

        return false;
    }
}
