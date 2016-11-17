<?php

use yii\db\Migration;

class m160228_153127_create_table_ordertype extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%ordertype}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'name' => 'string NOT NULL', 
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
