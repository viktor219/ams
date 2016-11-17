<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property string $categoryname
 * @property string $datecreated
 * @property string $datemodified
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%categories}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoryname'], 'required'],
            [['datecreated', 'datemodified'], 'safe'],
            [['categoryname'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'categoryname' => Yii::t('app', 'Category Name'),
            'datecreated' => Yii::t('app', 'Date Created'),
            'datemodified' => Yii::t('app', 'Date Modified'),
        ];
    }
}
