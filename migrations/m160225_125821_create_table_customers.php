<?php

use yii\db\Migration;

class m160225_125821_create_table_customers extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%customers}}';
	}
	
    public function up()
    {
		$this->before();
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(),
			'companyname' => 'VARCHAR(100) NOT NULL',              
			'contactname' => 'VARCHAR(100) NULL',    
			'phone' => 'VARCHAR(100) NULL',
			'email' => 'VARCHAR(100) NOT NULL',
			'trackserials' => 'TINYINT default 1',                           
			'requireordernumber' => 'TINYINT default 1',             
			'picture_id' => 'integer NULL',              
			'vert_picture_id' => 'integer NULL',              
			'defaultreceivinglocation' => 'VARCHAR(100) NULL',
			'defaultshippinglocation' => 'integer NOT NULL',
			'defaultbillinglocation' => 'integer NOT NULL',
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