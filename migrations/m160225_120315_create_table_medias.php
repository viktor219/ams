<?php

use yii\db\Migration;

class m160225_120315_create_table_medias extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%medias}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
			'filename' => 'string NOT NULL', 
			'path' => 'string NULL', // can be /customers/ || /departements/ || ...
			'description' => 'string NULL',
			'type' => 'integer NOT NULL DEFAULT 1',//1-->pictures || 2 -->audios || 3 --> videos.
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
