<?php

use yii\db\Migration;

class m160301_153742_create_table_item_requested extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%item_requested}}';
	}
	
    public function up()
    {
		$this->before();
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(),
			'description' => 'TEXT NOT NULL',              
			'manpartnum' => 'VARCHAR(100) NOT NULL',    
			'date_added' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
		], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}