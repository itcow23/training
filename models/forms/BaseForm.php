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
            [
                [$attribute],
                'file',
                'skipOnEmpty' => true,
                'maxFiles' => $maxFiles,
                'extensions' => 'jpg, jpeg, png, webp',
                'mimeTypes' => 'image/jpeg, image/png, image/webp',
            ],
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

    protected array $pushedAttributes = [];

    public function load($data, $formName = null): bool
    {
        if (!empty($data)) {
            $this->pushedAttributes = array_keys($formName === '' ? $data : ($data[$formName] ?? []));
        }
        return parent::load($data, $formName);
    }

    public function validateOnUpdate($attribute): void
    {
        if (in_array($attribute, $this->pushedAttributes) && ($this->$attribute === '' || $this->$attribute === null)) {
            $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be blank.');
        }
    }

    public function getPushedAttributes(): array
    {
        return $this->pushedAttributes;
    }
}
