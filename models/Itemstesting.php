<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%itemstesting}}".
 *
 * @property integer $id
 * @property integer $itemid
 * @property string $problem
 * @property integer $resolution
 * @property integer $partid
 * @property string $created_at
 * @property string $modified_at
 */
class Itemstesting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%itemstesting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemid', 'problem', 'resolution'], 'required'],
            [['itemid', 'partid'], 'integer'],
            [['problem', 'resolution'], 'string'],
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
            'itemid' => 'Itemid',
            'problem' => 'Problem',
            'resolution' => 'Resolution',
            'partid' => 'Partid',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
}
