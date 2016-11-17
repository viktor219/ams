<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "models_awg".
 *
 * @property integer $id
 * @property string $palletqtylimit
 * @property string $stripcharacters
 * @property string $checkit
 * @property string $manufacturer
 * @property string $descrip
 * @property string $image_path
 * @property string $aei
 * @property string $cust1partnum
 * @property string $cust2partnum
 * @property string $cust3partnum
 * @property string $frupartnum
 * @property string $manpartnum
 * @property string $category
 * @property string $department
 * @property string $serialized
 * @property string $storespecific
 * @property integer $quote
 * @property integer $is_showing_vendor
 */
class ModelsAwg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'models_awg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quote', 'is_showing_vendor'], 'integer'],
            [['palletqtylimit', 'stripcharacters', 'image_path', 'cust1partnum', 'cust2partnum', 'cust3partnum', 'frupartnum', 'manpartnum', 'category'], 'string', 'max' => 100],
            [['checkit'], 'string', 'max' => 20],
            [['manufacturer', 'department', 'serialized'], 'string', 'max' => 11],
            [['descrip'], 'string', 'max' => 255],
            [['aei'], 'string', 'max' => 50],
            [['storespecific'], 'string', 'max' => 55]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'palletqtylimit' => 'Palletqtylimit',
            'stripcharacters' => 'Stripcharacters',
            'checkit' => 'Checkit',
            'manufacturer' => 'Manufacturer',
            'descrip' => 'Descrip',
            'image_path' => 'Image Path',
            'aei' => 'Aei',
            'cust1partnum' => 'Cust1partnum',
            'cust2partnum' => 'Cust2partnum',
            'cust3partnum' => 'Cust3partnum',
            'frupartnum' => 'Frupartnum',
            'manpartnum' => 'Manpartnum',
            'category' => 'Category',
            'department' => 'Department',
            'serialized' => 'Serialized',
            'storespecific' => 'Storespecific',
            'quote' => 'Quote',
            'is_showing_vendor' => 'Is Showing Vendor',
        ];
    }
}
