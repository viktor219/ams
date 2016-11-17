<?php

use yii\db\Migration;
use yii\db\Schema;

class m160225_122134_add_comment_media_table extends Migration
{
	public $tableName;
	
	public function before()
	{
		$this->tableName = '{{%medias}}';
	}
	
    public function up()
    {
		$this->before();
		$this->alterColumn($this->tableName, 'path', Schema::TYPE_STRING . " NULL COMMENT 'can be /customers/ || /departements/ || ...'");
    }

    public function down()
    {
        echo "m160225_122134_add_comment_media_table cannot be reverted.\n";

        return false;
    }
}
