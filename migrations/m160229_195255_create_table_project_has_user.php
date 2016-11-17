<?php

use yii\db\Migration;

class m160229_195255_create_table_project_has_user extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%project_has_user}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'projectid' => 'integer NOT NULL',
            'userid' => 'integer NOT NULL',
			'date_joined' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'PRIMARY KEY (projectid,userid)'
        ], $this->MySqlOptions);	
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
