<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_medias".
 *
 * @property integer $id
 * @property string $filename
 * @property string $path
 * @property string $description
 * @property integer $type
 * @property string $created_at
 */
class Medias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'lv_medias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['filename'], 'required'],
            [['type', 'path', 'description', 'created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'path' => 'Path',
            'description' => 'Description',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }

}
