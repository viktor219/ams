<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_item_requested".
 *
 * @property integer $id
 * @property string $description
 * @property string $manpartnum
 * @property string $date_added
 */
class ItemRequested extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%itemsrequested}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'manpartnum'], 'required'],
            [['description'], 'string'],
            [['customer_id', 'date_added'], 'safe'],
            //[['manpartnum'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'manpartnum' => 'Manpartnum',
            'date_added' => 'Date Added',
        ];
    }
}
