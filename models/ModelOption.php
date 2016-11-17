<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_model_options".
 *
 * @property integer $id
 * @property integer $idmodel
 * @property string $name
 * @property integer $optiontype
 * @property integer $level
 * @property integer $parent_id
 * @property integer $checkable
 */
class ModelOption extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_model_options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'optiontype'], 'required'],
            [['idmodel', 'optiontype', 'level', 'parent_id', 'checkable'], 'integer'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idmodel' => 'Idmodel',
            'name' => 'Name',
            'optiontype' => 'Optiontype',
            'level' => 'Level',
            'parent_id' => 'Parent ID',
            'checkable' => 'Checkable',
        ];
    }
}
