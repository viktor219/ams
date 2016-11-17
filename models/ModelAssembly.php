<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%model_assemblies}}".
 *
 * @property integer $id
 * @property integer $modelid
 * @property integer $partid
 * @property integer $quantity
 * @property string $created_at
 * @property string $modified_at
 */
class ModelAssembly extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%model_assemblies}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerid', 'modelid', 'partid', 'quantity'], 'required'],
            [['modelid', 'partid', 'quantity'], 'integer'],
            [['created_at', 'modified_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modelid' => 'Assembly name',
            'partid' => 'Item',
            'quantity' => 'Quantity',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}