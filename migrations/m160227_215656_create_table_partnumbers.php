<?php

use yii\db\Migration;

class m160227_215656_create_table_partnumbers extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%partnumbers}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'customer' => 'integer NOT NULL', 
			'model' => 'integer NULL',
			'partid' => 'VARCHAR(100) NOT NULL',
			'partdescription' => 'TEXT NOT NULL',
			'type' => 'integer NULL',
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
