<?php

use yii\db\Migration;

class m160228_155619_add_column_order_type_table_shipment extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%shipments}}';
	}
	
    public function up()
    {
		$this->before();
		$this->addColumn($this->tableName, 'ordertype', 'integer NULL');
    }

    public function down()
    {
        echo "m160228_155619_add_column_order_type_table_shipment cannot be reverted.\n";

        return false;
    }
}