<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%shipment_methods}}".
 *
 * @property integer $id
 * @property integer $shipping_company_id
 * @property string $code
 * @property string $_key
 * @property string $_value
 * @property string $created_at
 * @property string $modified_at
 */
class ShipmentMethod extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shipment_methods}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shipping_company_id', 'code', '_key', '_value'], 'required'],
            [['shipping_company_id'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['_key', '_value'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shipping_company_id' => 'Shipping Company ID',
            'code' => 'Code',
            '_key' => 'Key',
            '_value' => 'Value',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
