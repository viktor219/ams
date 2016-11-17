<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "_l_divisions".
 *
 * @property integer $id
 * @property string $did
 * @property string $dname
 */
class Gimpdivision extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '_l_divisions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['did'], 'string', 'max' => 7],
            [['dname'], 'string', 'max' => 18]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'did' => 'Did',
            'dname' => 'Dname',
        ];
    }
}
