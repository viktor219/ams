<?php

use yii\db\Migration;

class m160228_154443_insert_table_ordertype extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%ordertype}}';
	}
	
    public function up()
    {
		$this->before();
		
		$this->insert($this->tableName,array(
			 'name'=>'Purchase (New In Box)',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Purchase (Refurbished)',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Purchase (As Is)',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Customer Repair',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Customer Integration',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Customer Warehousing',
		));
    }

    public function down()
    {
        echo "m160228_154443_insert_table_ordertype cannot be reverted.\n";

        return false;
    }
}
