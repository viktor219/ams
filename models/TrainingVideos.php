<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lv_training_videos".
 *
 * @property integer $id
 * @property string $title
 * @property string $filename
 * @property string $description
 * @property string $duration
 * @property string $created_at
 */
class TrainingVideos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lv_training_videos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'duration'], 'required'],
            [['description'], 'string'],
            [['created_at'], 'safe'],
            [['title', 'filename', 'duration'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'filename' => 'Filename',
            'description' => 'Description',
            'duration' => 'Duration',
            'created_at' => 'Created At',
        ];
    }
}
