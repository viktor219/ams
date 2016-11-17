<?php

use yii\db\Migration;

class m160225_095833_create_users_table extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%users}}';
	}
	
    public function safeUp()
    {
		$this->before();
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(),
			'firstname' => 'string NULL',              
			'lastname' => 'string NULL',    
			'email' => 'string NOT NULL',
			'username' => 'string NOT NULL',                            
			'hash_password' => 'string NOT NULL',              
			'usertype' => 'integer NOT NULL',              
			'department' => 'integer NULL',              
			'last_login' => 'DATETIME NOT NULL DEFAULT 0',
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'modified_at' => 'DATETIME NOT NULL DEFAULT 0',
		], $this->MySqlOptions);
    }

    public function safeDown()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
