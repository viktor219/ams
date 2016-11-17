<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%states}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $state
 * @property string $created_at
 */
class State extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%states}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'state'], 'required'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 5],
            [['state'], 'string', 'max' => 25]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'state' => 'State',
            'created_at' => 'Created At',
        ];
    }
}
