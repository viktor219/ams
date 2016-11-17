<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory".
 *
 * @property string $id
 * @property string $modelname
 * @property string $aeino
 * @property string $description
 * @property string $imagepath
 * @property string $manufacturer
 * @property string $department
 * @property string $category
 * @property string $palletqtylimit
 * @property string $stripcharacters
 * @property string $checkit
 * @property string $frupartnum
 * @property string $manpartnum
 * @property integer $istrackserial
 * @property string $isstorespecific
 * @property integer $quote
 * @property string $created_at
 * @property string $modified_at
 */
class Inventory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%models}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['istrackserial','manufacturer', 'department', 'category_id', 'quote'], 'integer'],
            [['created_at', 'modified_at', 'image_id'], 'safe'],
            [['palletqtylimit', 'stripcharacters', 'checkit', 'frupartnum', 'manpartnum'], 'string', 'max' => 100],
            [['aei'], 'string', 'max' => 50],
            [['isstorespecific'], 'string', 'max' => 55]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'aei' => Yii::t('app', 'AE#'),
            'description' => Yii::t('app', 'Description'),
            'image_id' => Yii::t('app', 'Image'),
            'manufacturer' => Yii::t('app', 'Manufacturer'),
            'department' => Yii::t('app', 'Department'),
            'category_id' => Yii::t('app', 'Category'),
            'palletqtylimit' => Yii::t('app', 'Pallet Quantity limit'),
            'stripcharacters' => Yii::t('app', 'Strip Characters'),
            'checkit' => Yii::t('app', 'Check Serial'),
            'frupartnum' => Yii::t('app', 'Fru Partnumber'),
            'manpartnum' => Yii::t('app', 'Manufacturer Partnumber'),
            'istrackserial' => Yii::t('app', 'Track Serial'),
            'isstorespecific' => Yii::t('app', 'Store Specific'),
            'quote' => Yii::t('app', 'Quote'),
            'created_at' => Yii::t('app', 'Date Created'),
            'modified_at' => Yii::t('app', 'Date Modified'),
        ];
    }
}
