<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_locations_classments".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $location_id
 * @property string $created_at
 */
class LocationClassment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_locations_classments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'location_id'], 'required'],
            [['parent_id', 'location_id'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'location_id' => 'Location ID',
            'created_at' => 'Created At',
        ];
    }
}
