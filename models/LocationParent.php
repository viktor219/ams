<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_locations_parent".
 *
 * @property integer $id
 * @property integer $parent_parent_id
 * @property string $parent_code
 * @property string $parent_name
 */
class LocationParent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_locations_parent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_parent_id'], 'integer'],
            [['parent_name'], 'required'],
            [['parent_code'], 'string', 'max' => 10],
            [['parent_name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_parent_id' => 'Parent Parent ID',
            'parent_code' => 'Parent Code',
            'parent_name' => 'Parent Name',
        ];
    }
}
