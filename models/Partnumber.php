<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_partnumbers".
 *
 * @property integer $id
 * @property integer $customer
 * @property integer $model
 * @property string $partid
 * @property string $partdescription
 * @property integer $type
 * @property string $created_at
 */
class Partnumber extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_partnumbers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer', 'partid', 'partdescription'], 'required'],
            [['customer', 'model', 'type'], 'integer'],
            [['partdescription'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['partid'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer' => 'Customer',
            'model' => 'Model',
            'partid' => 'Partid',
            'partdescription' => 'Partdescription',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $totalChangedAttr = count($changedAttributes);
        if($totalChangedAttr > 0){
            $sql = "select model,group_concat(DISTINCT partid) as partnumber from lv_partnumbers join lv_models on lv_models.id = lv_partnumbers.model where lv_models.id = ".$this->model;
            $connection = Yii::$app->getDb();
            $partNumbers = $connection->createCommand($sql)->queryOne();
            $changedAttributes = [];
            $changedAttributes["partnumber"] = $partNumbers["partnumber"];
            $changedAttributes['customer'] = (string)$this->customer;
            $db = \Yii::$app->common->getMongoDb();
            $collection = $db->inventorymodels;
            $collection->update(array('id' => (string)  $this->model), array('$set' => $changedAttributes));
        }
    }
}
