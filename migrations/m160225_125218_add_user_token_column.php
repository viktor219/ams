<?php

use yii\db\Migration;

class m160225_125218_add_user_token_column extends Migration
{
    public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%users}}';
	}
	
    public function up()
    {
		$this->before();
		$this->addColumn($this->tableName, 'token', 'CHAR(32) AFTER `usertype` ');
    }

    public function down()
    {
        echo "m160225_125218_add_user_token_column cannot be reverted.\n";

        return false;
    }
}
