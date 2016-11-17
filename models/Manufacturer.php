<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "manufacturers".
 *
 * @property integer $id
 * @property string $name
 * @property string $image_path
 */
class Manufacturer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%manufacturers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'image_path'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'image_path' => Yii::t('app', 'Image Path'),
        ];
    }
}
