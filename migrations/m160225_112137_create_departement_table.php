<?php

use yii\db\Migration;

class m160225_112137_create_departement_table extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%departements}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'name' => 'string NOT NULL', 
        ]);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}