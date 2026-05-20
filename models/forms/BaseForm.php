<?php

namespace app\models\forms;

use yii\base\Model;

class BaseForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_UPDATE_STATUS = 'update_status';

    public function scenarios()
    {
        return parent::scenarios();
    }

    public function validateArray($attribute): void
    {
        if ($this->$attribute !== null && !is_array($this->$attribute)) {

            $this->addArrayError($attribute);
        }
    }

    protected function addArrayError(string $attribute): void
    {
        $this->addError(
            $attribute,
            $this->getAttributeLabel($attribute) . ' must be an array.'
        );
    }

    protected function imageRules(string $attribute = 'image', int $maxFiles = 10): array
    {
        return [
            [[$attribute], 'file', 'skipOnEmpty' => true, 'maxFiles' => $maxFiles],
        ];
    }

    protected function removedImageRules(string $attribute = 'removed_image'): array
    {
        return [
            [[$attribute], 'safe', 'on' => self::SCENARIO_UPDATE],
            [[$attribute], 'each', 'rule' => ['integer']],
        ];
    }

    protected function uniqueNameRule(string $attribute, string $targetClass, string $targetAttribute = 'name', string $idAttribute = 'id'): array
    {
        return [
            [
                [$attribute],
                'unique',
                'targetClass' => $targetClass,
                'targetAttribute' => $targetAttribute,
                'filter' => function ($query) use ($idAttribute) {
                    if ($this->{$idAttribute}) {
                        $query->andWhere(['!=', $idAttribute, $this->{$idAttribute}]);
                    }
                },
            ],
        ];
    }

    // Note: default and string-max helpers removed; add explicit rules in forms instead.
}
