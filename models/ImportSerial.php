<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%import_serials}}".
 *
 * @property integer $id
 * @property string $serialnumber
 */
class ImportSerial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%import_serials}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serialnumber'], 'required'],
            [['serialnumber'], 'string', 'max' => 100],
            [['serialnumber'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'serialnumber' => 'Serialnumber',
        ];
    }
}
