<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_mitemslocation".
 *
 * @property integer $id
 * @property string $storenum
 * @property string $tagnumber
 * @property string $serialnumber
 * @property string $last_check_in
 * @property string $devicemodel
 * @property string $deviceos
 */
class LMitemlocation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_mitemslocation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storenum'], 'string', 'max' => 10],
            [['tagnumber'], 'string', 'max' => 13],
            [['serialnumber', 'last_check_in'], 'string', 'max' => 15],
            [['devicemodel'], 'string', 'max' => 6],
            [['deviceos'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storenum' => 'Storenum',
            'tagnumber' => 'Tagnumber',
            'serialnumber' => 'Serialnumber',
            'last_check_in' => 'Last Check In',
            'devicemodel' => 'Devicemodel',
            'deviceos' => 'Deviceos',
        ];
    }
}
