<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_reports_settings".
 *
 * @property integer $id
 * @property integer $userid
 * @property integer $report_type_id
 * @property integer $report_option_id
 * @property integer $is_division
 * @property string $created_at
 */
class ReportsSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_reports_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'report_type_id', 'report_option_id', 'created_at'], 'required'],
            [['userid', 'report_type_id', 'report_option_id', 'is_division'], 'integer'],
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
            'userid' => 'Userid',
            'report_type_id' => 'Report Type ID',
            'report_option_id' => 'Report Option ID',
            'is_division' => 'Is Division',
            'created_at' => 'Created At',
        ];
    }
}
