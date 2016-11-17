<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%models}}".
 *
 * @property integer $id
 * @property integer $palletqtylimit
 * @property string $stripcharacters
 * @property integer $checkit
 * @property integer $manufacturer
 * @property string $descrip
 * @property integer $image_id
 * @property string $aei
 * @property string $frupartnum
 * @property string $manpartnum
 * @property integer $category_id
 * @property integer $department
 * @property integer $serialized
 * @property integer $storespecific
 * @property integer $quote
 * @property integer $deleted
 * @property string $created_at
 * @property string $modified_at
 */
class Models extends \yii\db\ActiveRecord
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
            [['descrip', 'reorderqty', 'palletqtylimit', 'checkit', 'charactercount', 'manufacturer', 'image_id', 'category_id', 'department', 'serialized', 'assembly', 'storespecific', 'quote', 'islanesepecific', 'requiretestingreferb', 'preowneditems', 'prefered_vendor', 'secondary_vendor', 'old_asset_number', 'created_at', 'modified_at', 'purchasepricing', 'purchasepricingtier2', 'repairpricing', 'repairpricingtier2', 'merge_id', 'deleted'], 'safe'],
            [['stripcharacters', 'aei', 'frupartnum', 'manpartnum'], 'string', 'max' => 100],
            [['reorderqty', 'charactercount', 'deleted'],'default','value'=>0],
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
            'image_id' => 'Image ID',
            'aei' => 'Aei',
            'merge_id' => 'Merge Id',
            'frupartnum' => 'Frupartnum',
            'manpartnum' => 'Manpartnum',
            'category_id' => 'Category ID',
            'department' => 'Department',
            'serialized' => 'Serialized',
            'storespecific' => 'Storespecific',
            'quote' => 'Quote',
            'deleted' => 'Deleted',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
        ];
    }
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $totalChangedAttr = count($changedAttributes);
        if($insert){
                $db = \Yii::$app->common->getMongoDb();
                $collection = $db->inventorymodels;
                $manufacturer = Manufacturer::findOne($this->manufacturer);
                if($this->department){
                    $department = Department::findOne($this->department)->name;
                }
                $collection->insert([
                    'modelname' => $manufacturer->name.' '. $this->descrip,
                    'name' => $manufacturer->name,
                    'descrip' => $this->descrip,
                    'image_id' => $this->image_id,
                    'aei' => (string) $this->aei,
                    'frupartnum' => $this->frupartnum,
                    'manpartnum' => $this->manpartnum,
                    'deleted' => (string) $this->deleted,
                    'department' => $department,
                    'id' => (string) $this->id,
                    'category_id' => (string) $this->category_id,
                    'assembly' => (string) $this->assembly,
                    'instock_qty' => 0
                ]);
        } else {
            if($totalChangedAttr > 0){
                $db = \Yii::$app->common->getMongoDb();
                $collection = $db->inventorymodels;
                foreach($changedAttributes as $index => $value){
                    $changedAttributes[$index] = (string)$this->$index;
                    if($index == 'descrip' || $index == 'manufacturer'){
                        $manufacturer = Manufacturer::findOne($this->manufacturer);
                        $changedAttributes['modelname'] = $manufacturer->name.' '. $this->descrip;
                    }
                }
                $partnum = Partnumber::find()->where(['model' => $this->id])->one();
                if($partnum != NULL){
                    $partnumbers = Partnumber::find()
                            ->where(['model' => $this->id])
                            ->andWhere('partid is NOT NULL and partid !=""')
                            ->all();
                    $parts = [];
                    foreach ($partnumbers as $partnumber){
                        $parts[] = $partnumber->partid;
                    }
                    $query = "SELECT model, count(id) AS nb_models, SUM(IF(status='". array_search('In Stock', Item::$status)."',1,0)) AS instock_qty FROM lv_items WHERE status IN (". array_search('In Stock', Item::$status).") and lv_items.model = ".$this->id." GROUP BY model";
                    $data = Yii::$app->db->createCommand($query)->queryOne();
                    if($data != NULL){
                        $changedAttributes['nb_models'] = (string) $data['nb_models'];
                        $changedAttributes['instock_qty'] = (string) $data['instock_qty'];
                    }
                    $changedAttributes['partnumber'] = implode(",", $parts);
                    $changedAttributes['customer'] = [(string) $partnum->customer];
                }
                $collection->update(array('id' => (string)  $this->id), array('$set' => $changedAttributes));
            }
        }
        
        $recentActivity = New Recentactivity;
        $recentActivity->pk = $this->id;
        $recentActivity->user_id = Yii::$app->user->id;
        $recentActivity->created_at = date('Y-m-d H:i:s');
        $recentActivity->itemscount = 0;
        $recentActivity->type = array_search('Model', Recentactivity::$type);
        if($insert){
            $recentActivity->is_new = 1;
            $recentActivity->save(false);
        } elseif(count($changedAttributes)){
            $recentActivity->is_new = 0;
            $recentActivity->save(false);
        }
    }
}
