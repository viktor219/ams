<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pricing_import}}".
 *
 * @property string $itemid
 * @property string $itemdescription
 * @property integer $manufacturer
 * @property string $taxtype
 * @property string $_primary
 * @property string $tier2
 * @property string $por
 * @property string $price_chopper
 * @property string $dieberg
 * @property string $tolt
 * @property string $giant
 * @property string $ncr
 * @property string $tops
 */
class PricingImport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pricing_import}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemid'], 'required'],
            [['manufacturer'], 'integer'],
            [['itemid'], 'string', 'max' => 20],
            [['itemdescription'], 'string', 'max' => 30],
            [['taxtype'], 'string', 'max' => 7],
            [['_primary', 'por', 'tolt', 'giant', 'ncr', 'tops'], 'string', 'max' => 8],
            [['tier2', 'price_chopper', 'dieberg'], 'string', 'max' => 6]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'itemid' => 'Itemid',
            'itemdescription' => 'Itemdescription',
            'manufacturer' => 'Manufacturer',
            'taxtype' => 'Taxtype',
            '_primary' => 'Primary',
            'tier2' => 'Tier2',
            'por' => 'Por',
            'price_chopper' => 'Price Chopper',
            'dieberg' => 'Dieberg',
            'tolt' => 'Tolt',
            'giant' => 'Giant',
            'ncr' => 'Ncr',
            'tops' => 'Tops',
        ];
    }
}
