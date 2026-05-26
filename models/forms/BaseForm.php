<?php

namespace app\models\forms;

use yii\base\Model;

class BaseForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_UPDATE_STATUS = 'update_status';

    protected function imageRules(string $attribute = 'image', int $maxFiles = 10): array
    {
        return [
            [
                [$attribute],
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => $maxFiles,
                'extensions' => ['jpg', 'jpeg', 'png', 'webp'],
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ],
                'maxSize' => 5 * 1024 * 1024,
            ],
        ];
    }

    protected function removedImageRules(string $attribute = 'removed_image'): array
    {
        return [
            [[$attribute], 'each', 'rule' => ['integer']],
        ];
    }

    protected function uniqueNameRule(string $attribute, string $targetClass, string $targetAttribute = 'name'): array
    {
        return [
            [
                [$attribute],
                'unique',
                'targetClass' => $targetClass,
                'targetAttribute' => $targetAttribute,
                'filter' => function ($query) {
                    if (!$this->isNewRecordLike()) {
                        $query->andWhere([
                            '!=',
                            'id',
                            $this->id,
                        ]);
                    }
                },
            ],
        ];
    }

    protected function isNewRecordLike(): bool
    {
        return empty($this->id);
    }
}
