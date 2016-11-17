<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_serials_tagnumber".
 *
 * @property integer $id
 * @property string $tagnum
 * @property string $serial
 */
class ISerialTagnumber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_serials_tagnumber';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagnum'], 'string', 'max' => 7],
            [['serial'], 'string', 'max' => 19]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tagnum' => 'Tagnum',
            'serial' => 'Serial',
        ];
    }
}
