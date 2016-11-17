<?php

use yii\db\Migration;

class m160304_130722_insert_table_items_status extends Migration
{
	
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%items_status}}';
	}

    public function safeUp()
    {
		$this->before();
		
		$this->insert($this->tableName,array(
			'status'=>'instock',
		));
		
		$this->insert($this->tableName,array(
			'status'=>'inprogress',
		));
		
		$this->insert($this->tableName,array(
			'status'=>'readytoship',
		));
		
		$this->insert($this->tableName,array(
			'status'=>'shipped',
		));
    }

    public function safeDown()
    {
        echo "m160304_130722_insert_table_items_status cannot be reverted.\n";

        return false;
    }
}