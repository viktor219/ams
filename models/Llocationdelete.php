<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_locationsdelete".
 *
 * @property integer $id
 * @property string $division_id
 * @property string $storenum
 * @property string $storename
 * @property integer $zipcode
 * @property string $notes
 */
class Llocationdelete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_locationsdelete';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zipcode'], 'integer'],
            [['division_id'], 'string', 'max' => 2],
            [['storenum'], 'string', 'max' => 6],
            [['storename'], 'string', 'max' => 49],
            [['notes'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_id' => 'Division ID',
            'storenum' => 'Storenum',
            'storename' => 'Storename',
            'zipcode' => 'Zipcode',
            'notes' => 'Notes',
        ];
    }
}
