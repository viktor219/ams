<?php

use yii\db\Migration;

class m160225_135319_create_table_user_has_customer extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%user_has_customer}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'userid' => 'integer NOT NULL',
            'customerid' => 'integer NOT NULL',
			'date_joined' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'PRIMARY KEY (userid,customerid)'
        ], $this->MySqlOptions);		
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
