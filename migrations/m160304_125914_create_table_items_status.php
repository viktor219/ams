<?php

use yii\db\Migration;

class m160304_125914_create_table_items_status extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%items_status}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'status' => 'string NOT NULL', 
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}