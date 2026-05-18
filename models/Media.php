<?php

namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "media".
 *
 * @property int $id
 * @property int $file_id
 * @property string $file_type
 * @property string $filepath
 * @property string|null $caption
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Media extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caption', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['file_id', 'file_type', 'filepath'], 'required'],
            [['file_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['file_type', 'filepath', 'caption'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'file_type' => 'File Type',
            'filepath' => 'Filepath',
            'caption' => 'Caption',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    #[Override]
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::class,
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ]);
    }
}
