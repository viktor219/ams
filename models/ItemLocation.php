<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%items_location}}".
 *
 * @property integer $id
 * @property string $DivisionID
 * @property string $storenum
 * @property string $storename
 * @property string $model
 * @property string $serial
 * @property string $tagnum
 * @property string $shipped
 */
class ItemLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%items_location}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DivisionID'], 'string', 'max' => 7],
            [['storenum', 'shipped'], 'string', 'max' => 10],
            [['storename'], 'string', 'max' => 52],
            [['model'], 'string', 'max' => 33],
            [['serial'], 'string', 'max' => 15],
            [['tagnum'], 'string', 'max' => 8]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DivisionID' => 'Division ID',
            'storenum' => 'Storenum',
            'storename' => 'Storename',
            'model' => 'Model',
            'serial' => 'Serial',
            'tagnum' => 'Tagnum',
            'shipped' => 'Shipped',
        ];
    }
}
