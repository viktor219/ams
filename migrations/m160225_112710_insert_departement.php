<?php

use yii\db\Migration;

class m160225_112710_insert_departement extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%departements}}';
	}
	
    public function up()
    {
		$this->before();
		
		$this->insert($this->tableName,array(
			 'name'=>'Print',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Display & Touch',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Mobile & Wireless',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Cash & Coin',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Peripherals',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Terminal & Kiosk',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Self Checkout & Server',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Integration',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Radio',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Misc.',
		));
		
		$this->insert($this->tableName,array(
			 'name'=>'Warehousing',
		));
    }

    public function down()
    {
        echo "m160225_112710_insert_departement cannot be reverted.\n";

        return false;
    }
}
