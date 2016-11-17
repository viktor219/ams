<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%items_tagnumber}}".
 *
 * @property integer $id
 * @property string $Tag Number
 * @property string $Serial Number
 */
class ItemTagnumber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%items_tagnumber}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Tag Number'], 'string', 'max' => 8],
            [['Serial Number'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Tag Number' => 'Tag  Number',
            'Serial Number' => 'Serial  Number',
        ];
    }
}
