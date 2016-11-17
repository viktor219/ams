<?php

use yii\db\Migration;

class m160305_081022_create_user_add_tracker_columns extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%users}}';
	}
	
    public function up()
    {
		$this->before();
		$this->addColumn($this->tableName, 'default_mac_address', 'CHAR(32) AFTER `department`');
		$this->addColumn($this->tableName, 'default_ip_address', 'CHAR(15) AFTER `default_mac_address`');
    }

    public function down()
    {
        echo "m160225_125218_add_user_tracker_columns cannot be reverted.\n";

        return false;
    }
}
