<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%models_pictures}}".
 *
 * @property integer $id
 * @property string $_key
 * @property integer $modelid
 * @property integer $mediaid
 * @property string $date_added
 */
class ModelsPicture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%models_pictures}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_key','modelid', 'mediaid',  'date_added'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            '_key' => 'Key',
            'modelid' => 'Modelid',
            'mediaid' => 'Mediaid',
            'date_added' => 'Date Added',
        ];
    }
}
