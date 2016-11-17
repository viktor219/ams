<?php

use yii\db\Migration;

class m160225_141858_user_has_customer_foreign_key extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%user_has_customer}}';
	}
	
    public function safeUp()
    {
		$this->before();
		//
		$this->createIndex('f_user_id', $this->tableName , 'userid', true);
        $this->addForeignKey('fk__userid', $this->tableName, 'userid', 'lv_users', 'id', 'CASCADE', 'CASCADE');
		//
		$this->createIndex('f_customer_id', $this->tableName , 'customerid', true);
        $this->addForeignKey('fk_customerid', $this->tableName, 'customerid', 'lv_customers', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
		$this->before();
		//
		$this->dropForeignKey('fk__userid', $this->tableName);
		$this->dropIndex('f_user_id', $this->tableName);
		//
		$this->dropForeignKey('fk_customerid', $this->tableName);
		$this->dropIndex('f_customer_id', $this->tableName);
    }
}