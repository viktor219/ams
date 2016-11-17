<?php

use yii\db\Migration;

class m160225_132636_create_table_locations extends Migration
{
	protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_general_ci';
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%locations}}';
	}
	
    public function up()
    {
		$this->before();
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),     
			'customer_id' => 'integer NULL',
			'storename' => 'VARCHAR(100) NULL',              
			'storenum' => 'VARCHAR(100) NULL',    
			'address' => 'VARCHAR(100) NOT NULL',
			'address2' => 'VARCHAR(100) NULL',
			'country' => 'VARCHAR(100) NULL',
			'city' => 'VARCHAR(100) NOT NULL',
			'state' => 'VARCHAR(100) NOT NULL',
			'zipcode' => 'VARCHAR(100) NOT NULL',
			'phone' => 'VARCHAR(100) NULL',              
			'email' => 'VARCHAR(100) NULL',    
			'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
    }

    public function down()
    {
		$this->before();
        $this->dropTable($this->tableName);
    }
}
