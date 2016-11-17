<?php

use yii\db\Migration;

class m160228_170435_create_table_models extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%models}}';
	}
	
    public function up()
    {
		$this->before();
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(),
			'palletqtylimit' => 'integer NULL',              
			'stripcharacters' => 'VARCHAR(100) NULL',    
			'checkit' => 'integer NULL',
			'manufacturer' => 'integer NULL',
			'descrip' => 'TEXT NULL',                           
			'image_id' => 'integer NOT NULL',             
			'aei' => 'VARCHAR(100) NULL',              
			'frupartnum' => 'VARCHAR(100) NULL',              
			'manpartnum' => 'VARCHAR(100) NULL',
			'category_id' => 'integer NULL',
			'department' => 'integer NULL',
			'serialized' => 'TINYINT default 0',
			'storespecific' => 'integer NULL',
			'quote' => 'integer NULL',
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'modified_at' => 'DATETIME NOT NULL DEFAULT 0',
		], $this->MySqlOptions);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
