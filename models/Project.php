<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property integer $id
 * @property string $company
 * @property string $contact
 * @property string $phone
 * @property string $email
 * @property integer $trackserials
 * @property integer $requireordernumber
 * @property string $image_path
 * @property string $vert_image_path
 * @property string $defaultreceivinglocation
 * @property string $defaultshippinglocation
 * @property string $defaultbillinglocation
 * @property string $created_at
 * @property string $updated_at
 */
class Project extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%customers}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['companyname'], 'required'],
            [['defaultshippinglocation', 'defaultbillinglocation', 'defaultreceivinglocation', 'vert_picture_id', 'picture_id', 'trackserials', 'requireordernumber', 'firstname', 'lastname', 'phone', 'created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
        ];
    }

}
