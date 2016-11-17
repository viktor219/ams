<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_invoices".
 *
 * @property integer $id
 * @property string $invoicename
 * @property integer $orderid
 * @property integer $generated
 * @property string $created_at
 */
class Invoices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_invoices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoicename'], 'required'],
            [['orderid', 'generated'], 'integer'],
            [['created_at'], 'safe'],
            [['invoicename'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoicename' => 'Invoicename',
            'orderid' => 'Orderid',
            'generated' => 'Generated',
            'created_at' => 'Created At',
        ];
    }
}
