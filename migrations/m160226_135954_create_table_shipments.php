<?php

use yii\db\Migration;

class m160226_135954_create_table_shipments extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%shipments}}';
	}
	
    public function up()
    {
		$this->before();
		$this->createTable($this->tableName, [
			'id' => $this->primaryKey(),
			'customer_id' => 'integer NULL',              
			'location_id' => 'integer NULL',    
			'number_generated' => 'VARCHAR(100) NULL',
			'number_radius' => 'VARCHAR(100) NULL',                            
			'number_bb' => 'VARCHAR(100) NULL',              
			'notes' => 'TEXT NULL',              
			'type' => 'integer NOT NULL DEFAULT 1',     
			'returned' => 'TINYINT default 0',
			'returneddate' => 'DATETIME NULL',
			'trackingnumber' => 'VARCHAR(100) NULL',   
			'trackinglink' => 'integer NULL',   
			'dateshipped' => 'DATETIME NULL',   
			'shipby' => 'DATETIME NULL',   
			'trucknum' => 'VARCHAR(55) NULL',   
			'sealnum' => 'VARCHAR(55) NULL',   
			'dateonsite' => 'DATETIME NULL',   
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
			'modified_at' => 'DATETIME NOT NULL DEFAULT 0',
        ]);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}