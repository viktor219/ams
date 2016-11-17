<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%update_items_locations}}".
 *
 * @property string $storenum
 * @property string $tagnum
 * @property string $serial
 */
class UpdateItemLocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%update_items_locations}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storenum'], 'string', 'max' => 8],
            [['tagnum'], 'string', 'max' => 7],
            [['serial'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'storenum' => 'Storenum',
            'tagnum' => 'Tagnum',
            'serial' => 'Serial',
        ];
    }
}
