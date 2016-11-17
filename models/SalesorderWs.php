<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%salesorders_ws}}".
 *
 * @property integer $id
 * @property integer $warehouse_id
 * @property integer $service_id
 * @property string $date_added
 */
class SalesorderWs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%salesorders_ws}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'service_id'], 'required'],
            [['warehouse_id', 'service_id'], 'integer'],
            [['date_added'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Warehouse ID',
            'service_id' => 'Service ID',
            'date_added' => 'Date Added',
        ];
    }
}
