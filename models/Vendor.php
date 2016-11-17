<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%vendor}}".
 *
 * @property integer $id
 * @property string $vendorid
 * @property string $vendorname
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $zip
 * @property string $state
 * @property string $contact
 * @property string $telephone_1
 * @property string $telephone_2
 * @property string $fax
 * @property integer $1099_type
 * @property integer $taxidno
 * @property string $terms
 * @property integer $active
 * @property integer $usebillpay
 * @property string $accountno
 * @property string $email
 * @property string $website
 * @property integer $expense_account_id
 * @property string $last_inv_amt
 * @property string $notes
 * @property string $date_joined
 */
class Vendor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vendor}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendorid', 'vendorname', 'address_line_1', 'city', 'zip', 'contact'], 'required'],
            [['1099_type', 'taxidno', 'active', 'usebillpay', 'expense_account_id'], 'integer'],
            [['last_inv_amt'], 'number'],
            [['notes'], 'string'],
            [['date_joined'], 'safe'],
            [['vendorid'], 'string', 'max' => 5],
            [['vendorname', 'address_line_1', 'address_line_2', 'zip', 'email', 'website'], 'string', 'max' => 255],
            [['city', 'state', 'contact', 'telephone_1', 'telephone_2', 'fax', 'terms', 'accountno'], 'string', 'max' => 120]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vendorid' => 'Code',
            'vendorname' => 'Vendor',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'city' => 'City',
            'zip' => 'Zip',
            'state' => 'State',
            'contact' => 'Contact',
            'telephone_1' => 'Telephone 1',
            'telephone_2' => 'Telephone 2',
            'fax' => 'Fax',
            '1099_type' => '1099 Type',
            'taxidno' => 'Taxidno',
            'terms' => 'Terms',
            'active' => 'Active',
            'usebillpay' => 'Usebillpay',
            'accountno' => 'Accountno',
            'email' => 'Email',
            'website' => 'Website',
            'expense_account_id' => 'Expense Account ID',
            'last_inv_amt' => 'Last Inv Amt',
            'notes' => 'Notes',
            'date_joined' => 'Date Joined',
        ];
    }
}
