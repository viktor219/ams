<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_reports_log".
 *
 * @property integer $id
 * @property integer $report_type_id
 * @property integer $customer_id
 * @property string $date_sent
 */
class ReportsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_reports_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_type_id', 'customer_id'], 'required'],
            [['report_type_id', 'customer_id'], 'integer'],
            [['date_sent'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_type_id' => 'Report Type ID',
            'customer_id' => 'Customer ID',
            'date_sent' => 'Date Sent',
        ];
    }
}
