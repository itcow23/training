<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "permission".
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property RolePermission[] $rolePermissions
 */
class Permission extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['name', 'key'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'key'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'key' => 'Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[RolePermissions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRolePermissions()
    {
        return $this->hasMany(RolePermission::class, ['permission_id' => 'id']);
    }

}
