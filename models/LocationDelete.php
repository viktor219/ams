<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%locations_delete}}".
 *
 * @property string $Division ID
 * @property string $Store Number
 * @property string $Store Name
 * @property integer $Zipcode
 */
class LocationDelete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%locations_delete}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Zipcode'], 'integer'],
            [['Division ID'], 'string', 'max' => 2],
            [['Store Number'], 'string', 'max' => 6],
            [['Store Name'], 'string', 'max' => 49]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Division ID' => 'Division  ID',
            'Store Number' => 'Store  Number',
            'Store Name' => 'Store  Name',
            'Zipcode' => 'Zipcode',
        ];
    }
}
