<?php

namespace App\UseCases\ModelValidators\Abstracts;

use App\DataResultObjects\Generic\ResultDRO as Result;
use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO;
use App\DataTransferObjects\ModelValidators\CustomFunctionValidatorDTO;
use App\Models\Abstracts\AbstractModel;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Traits\Validator\ValidatorConstantsTrait;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;

abstract class ModelAttributesValidatorAbstractUseCase implements ModelAttributesValidatorUseCaseInterface
{
    use ValidatorConstantsTrait;

    /**
     * @var AttributesValidatorDTO[] The configuration array for the model attributes.
     */
    private array $attributesConfig   = [];
    private array $validationErrors   = [];
    private array $validationWarnings = [];
    private array $attributesSent     = [];

    private AbstractModel $entity;

    /**
     * @inheritDoc
     */
    public function __invoke(array $attributes, AbstractModel $entity): Result
    {
        $this->initializeCustomValidators();
        $this->attributesConfig = $this->getAttributesConfig();
        $this->attributesSent = $attributes;
        $this->entity = $entity;

        $this->validateRequiredAttributes();
        $this->validateAttributesSent();

        if (!empty($this->validationErrors)) {
            return Result::createFailure($this->validationErrors, $this->validationWarnings);
        }

        return Result::createSuccess($this->attributesSent)->setWarnings($this->validationWarnings);
    }

    /**
     * Initializes the custom validators for the model attributes.
     *
     * This method should be implemented by the child classes to define and configure
     * the custom validators for the model attributes.
     *
     * @return void
     */
    protected function initializeCustomValidators(): void
    {}

    /**
     * Returns the configuration array for the model attributes.
     *
     * @return AttributesValidatorDTO[] The configuration array for the model attributes.
     */
    abstract protected function getAttributesConfig(): array;

    /**
     * Returns the model repository instance.
     *
     * @return ModelRepositoryInterface The model repository instance.
     */
    abstract protected function getModelRepository(): ModelRepositoryInterface;

    /**
     * Validates the required attributes.
     */
    private function validateRequiredAttributes()
    {
        foreach ($this->attributesConfig as $attribute => $attributeConfig) {
            if (!$attributeConfig->required) {
                continue;
            }
            $attributeValueSent = $this->attributesSent[$attribute] ?? null;
            $attributeSent = array_key_exists($attribute, $this->attributesSent);
            $inEntity = !is_null($this->entity->{$attribute});

            $valueSentIsNull = $attributeSent && is_null($attributeValueSent);
            $valueMissing = !$attributeSent && !$inEntity;
            
            if ($valueSentIsNull || $valueMissing) {
                $this->validationErrors[] = sprintf(
                    'El parámetro %s es obligatorio',
                    $attribute
                );
            }
        }
    }

    /**
     * Validates the types and restrictions of the model attributes.
     *
     * @return void
     */
    private function validateAttributesSent(): void
    {
        foreach ($this->attributesSent as $attribute => $attributeValue) {

            if (!$attributeConfig = $this->attributesConfig[$attribute] ?? null) {
                $this->validationWarnings[] = sprintf(
                    'Se ha ignorado el atributo %s por no hacer parte de la entidad',
                    $attribute
                );
                unset($this->attributesSent[$attribute]);
                continue;
            }

            if (is_null($attributeValue)) {
                continue;
            }

            $attributeType = $attributeConfig->type;
            $restrictionsAllowed = [];
            $restrictionsAllowed[] = self::RESTRICTION_REQUIRED;
            $restrictionsAllowed[] = self::RESTRICTION_UNIQUE;
            $restrictionsAllowed[] = self::RESTRICTION_IN_VALUES;
            if (!$this->validateAttributeType($attributeType, $attributeValue, $restrictionsAllowed)) {
                $this->validationErrors[] = sprintf(
                    'El valor "%s" para el atributo %s de tipo %s es incorrecto',
                    $this->getAttributeStrValue($attributeValue),
                    $attribute,
                    $attributeType
                );
                continue;
            }

            $attributeRestrictions = $attributeConfig->restrictions;
            $this->validateAttributeRestrictions($attribute, $attributeType, $attributeValue, $attributeRestrictions, $restrictionsAllowed);
            
            $customValidation = $attributeConfig->callCustomValidator(CustomFunctionValidatorDTO::create($this->entity, $attributeValue));
            $this->validationErrors = array_merge($this->validationErrors, $customValidation->errors);
            $this->validationWarnings = array_merge($this->validationWarnings, $customValidation->warnings);
        }
    }

    /**
     * Validates the type of an attribute.
     *
     * @param string $attributeType The type of the attribute.
     * @param mixed $attributeValue The value of the attribute.
     * @param array $restrictionsAllowed The allowed restrictions for the attribute.
     * 
     * @return bool Returns true if the attribute type is valid, false otherwise.
     */
    private function validateAttributeType($attributeType, &$attributeValue, &$restrictionsAllowed): bool
    {
        $typeValidated = false;
        switch($attributeType) {
            case self::TYPE_STRING:
                $typeValidated = $this->validateAtributeTypeString($attributeValue);
                $restrictionsAllowed[] = self::RESTRICTION_MAX_LENGTH;
                $restrictionsAllowed[] = self::RESTRICTION_MIN_LENGTH;
                break;
            case self::TYPE_INT:
                $typeValidated = $this->validateAttributeTypeInt($attributeValue);
                $restrictionsAllowed[] = self::RESTRICTION_MAX_VALUE;
                $restrictionsAllowed[] = self::RESTRICTION_MIN_VALUE;
                break;
            case self::TYPE_BOOL:
                $typeValidated = is_bool($attributeValue);
                break;
            case self::TYPE_ARRAY:
                $typeValidated = is_array($attributeValue);
                break;
        }

        return $typeValidated;
    }

    /**
     * Validates the restrictions for a given attribute.
     *
     * @param string $attribute The name of the attribute.
     * @param string $attributeType The type of the attribute.
     * @param mixed $attributeValue The value of the attribute.
     * @param array $attributeRestrictions The restrictions for the attribute.
     * @param array $restrictionsAllowed The allowed restrictions.
     * 
     * @return void
     */
    private function validateAttributeRestrictions($attribute, $attributeType, $attributeValue, array $attributeRestrictions, array $restrictionsAllowed)
    {
        foreach ($attributeRestrictions as $attributeRestriction => $restrictionValue) {
            if (!in_array($attributeRestriction, $restrictionsAllowed)) {
                continue;
            }
            $restrictionValidated = true;
            $restrictionMessage = '';
            switch ($attributeRestriction) {
                case self::RESTRICTION_MIN_LENGTH :
                    $restrictionValidated = $this->validateAttributeRestrictionMinLength($attributeType, $attributeValue, $restrictionValue);
                    $restrictionMessage = sprintf('longitud mínima (%s)', $restrictionValue);
                    break;
                case self::RESTRICTION_MAX_LENGTH:
                    $restrictionValidated = $this->validateAttributeRestrictionMaxLength($attributeType, $attributeValue, $restrictionValue);
                    $restrictionMessage = sprintf('longitud máxima (%s)', $restrictionValue);
                    break;
                case self::RESTRICTION_MIN_VALUE:
                    $restrictionValidated = $attributeValue >= $restrictionValue;
                    $restrictionMessage = sprintf('valor mínimo (%s)', $restrictionValue);
                    break;
                case self::RESTRICTION_MAX_VALUE:
                    $restrictionValidated = $attributeValue <= $restrictionValue;
                    $restrictionMessage = sprintf('valor máximo (%s)', $restrictionValue);
                    break;
                case self::RESTRICTION_UNIQUE:
                    $restrictionMessage = 'valor único, ya existe un registro con el mismo valor';
                    $restrictionValidated = $this->validateUniqueRestriction($attribute, $attributeValue);
                    break;
                case self::RESTRICTION_IN_VALUES:
                    $restrictionMessage = sprintf('valor permitido (%s)', implode(', ', $restrictionValue));
                    $restrictionValidated = in_array($attributeValue, $restrictionValue);
                    break;
            }

            if (!$restrictionValidated) {
                $this->validationErrors[] = sprintf(
                    'El valor "%s" para el atributo %s no cumple con la restricción %s',
                    $this->getAttributeStrValue($attributeValue),
                    $attribute,
                    $restrictionMessage
                );
            }
            
        }
    }

    /**
     * Checks if the attribute value is of type string.
     *
     * @param string $attributeValue The attribute value to be checked.
     * 
     * @return bool Returns true if the attribute value is of type string, false otherwise.
     */
    private function validateAtributeTypeString(&$attributeValue): bool
    {
        if (is_scalar($attributeValue)) {
            $attributeValue = ''.$attributeValue;
            return true;
        }
        return false;
    }

    /**
     * Checks if the attribute value is of type integer.
     *
     * @param int &$attributeValue The attribute value to be checked.
     * 
     * @return bool Returns true if the attribute value is of type integer, false otherwise.
     */
    private function validateAttributeTypeInt(&$attributeValue): bool
    {
        if (is_int($attributeValue)) {
            return true;
        }   
        if (is_string($attributeValue) && ctype_digit($attributeValue)) {
            $attributeValue = intval($attributeValue);
            return true;
        }
        return false;
    }

    /**
     * Checks if the attribute value meets the minimum length restriction.
     *
     * @param string $attributeType The type of the attribute.
     * @param mixed $attributeValue The value of the attribute.
     * @param int $minLength The minimum length restriction.
     * 
     * @return void
     */
    private function validateAttributeRestrictionMinLength($attributeType, $attributeValue, int $minLength): bool
    {
        $attributeLength = $this->getAttributeLength($attributeType, $attributeValue);
        return $attributeLength >= $minLength;
    }

    /**
     * Checks if the attribute value does not exceed the maximum length restriction.
     *
     * @param string $attributeType The type of the attribute.
     * @param mixed $attributeValue The value of the attribute.
     * @param int $maxValue The maximum length allowed for the attribute value.
     * 
     * @return void
     */
    private function validateAttributeRestrictionMaxLength($attributeType, $attributeValue, int $maxValue): bool
    {
        $attributeLength = $this->getAttributeLength($attributeType, $attributeValue);
        return $attributeLength <= $maxValue;
    }

    /**
     * Validates the unique restriction for a given attribute.
     *
     * @param string $attributeName The name of the attribute to validate.
     * @param mixed $attributeValue The value of the attribute to validate.
     * ß
     * @return void
     */
    private function validateUniqueRestriction($attributeName, $attributeValue): bool
    {
        $currentEntity = $this->getModelRepository()->findOneBy([
            $attributeName => $attributeValue
        ]);

        return !$currentEntity || ($currentEntity->id == $this->entity->id);
    }

    /**
     * Get the length of an attribute.
     *
     * @param string $attributeType The type of the attribute.
     * @param mixed $attributeValue The value of the attribute.
     * 
     * @return int The length of the attribute.
     */
    private function getAttributeLength($attributeType, $attributeValue)
    {
        $attributeLength = 0;
        switch ($attributeType) {
            case self::TYPE_STRING:
                $attributeLength = strlen($attributeValue);
                break;
        }

        return $attributeLength;
    }

    /**
     * Retrieves the string value of an attribute.
     *
     * @param mixed $attributeValue The value of the attribute.
     * 
     * @return string The string representation of the attribute value.
     */
    private function getAttributeStrValue($attributeValue)
    {
        if (is_scalar($attributeValue)) {
            return '' . $attributeValue;
        }
        if (is_array($attributeValue)) {
            return sprintf('array(%s)', sizeof($attributeValue));
        }
        if (is_object($attributeValue)) {
            return sprintf('objeto(%s)', get_class($attributeValue));
        }     
        return 'desconocido';
    }
}