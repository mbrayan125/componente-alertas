<?php

namespace App\UseCases\Models\Abstracts;

use App\Exceptions\Model\IncorrectAttributesException;
use App\Exceptions\Model\ModelValidationException;
use App\Models\Abstracts\AbstractModel;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\UseCases\Models\Contracts\CreateUpdateModelUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class CreateUpdateModelAbstractUseCase implements CreateUpdateModelUseCaseInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(array $attributes, AbstractModel $current = null): AbstractModel
    {
        DB::beginTransaction();
        try {
            // Attributes validation
            $modelInstance = $current ?? $this->createInstance();
            $validation = ($this->getModelAttributesValidator())($attributes, $modelInstance);
            if (!$validation->success) {
                throw new IncorrectAttributesException(
                    $this->getModelAttributesValidator()->getModelName(), 
                    $validation->errors, 
                    $validation->warnings
                );
            }

            // Separate the attributes that are not part of the model
            $preparedAttributes = $validation->data;
            $extraData = $this->determineExtraData($attributes, $preparedAttributes);

            // Create the new instance with actions
            $this->preFillActions($modelInstance, $preparedAttributes, $extraData);
            $modelInstance->fillData($preparedAttributes);
            $this->postFillActions($modelInstance, $preparedAttributes, $extraData);

            // Save the new instance with actions
            $this->getModelRepository()->save($modelInstance);
            $this->postSaveActions($modelInstance, $preparedAttributes, $extraData);
            $this->getModelRepository()->save($modelInstance);
            DB::commit();

        } catch (Throwable $exception) {

            // Rollback the transaction
            DB::rollBack();
            $this->handleRollback($modelInstance, $exception);
            if ($exception instanceof ModelValidationException) {                
                throw $exception;
            }
            throw new Exception('Error desconocido al crear el modelo', 0, $exception);
        }

        return $modelInstance;
    }

    /**
     * Returns the instance of the model attributes validator.
     *
     * @return ModelAttributesValidatorUseCaseInterface The model attributes validator instance.
     */
    abstract protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface;

    /**
     * Creates an instance of the AbstractModel.
     *
     * @return AbstractModel The created instance of the AbstractModel.
     */
    abstract protected function createInstance(): AbstractModel;

    /**
     * Returns the model repository instance.
     *
     * @return ModelRepositoryInterface The model repository instance.
     */
    abstract protected function getModelRepository(): ModelRepositoryInterface;

    /**
     * Pre-fills the actions for creating a new instance of the AbstractModel.
     *
     * @param AbstractModel $modelInstance The new instance of the AbstractModel.
     * @param array $attributes The attributes to be filled in the new instance.
     * @param array $extraData Additional data to be used for pre-filling actions.
     * 
     * @return void
     */
    protected function preFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    { }

    /**
     * Performs post-fill actions on a newly created model instance.
     *
     * @param AbstractModel $modelInstance The newly created model instance.
     * @param array $attributes The attributes used to create the model.
     * @param array $extraData Additional data for post-fill actions.
     * 
     * @return void
     */
    protected function postFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    { }

    /**
     * Performs post-save actions after creating a new instance of AbstractModel.
     *
     * @param AbstractModel $modelInstance The newly created instance of AbstractModel.
     * @param array $attributes The attributes used to create the instance.
     * @param array $extraData Additional data for post-save actions.
     * 
     * @return void
     */
    protected function postSaveActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    { }

    /**
     * Handles the rollback operation in case of an exception.
     *
     * @param Throwable $ex The exception that occurred.
     * @return void
     */
    protected function handleRollback(AbstractModel $modelInstance, Throwable $ex): void
    { }

    /**
     * Determines the extra data based on the given attributes and prepared attributes.
     *
     * @param array $attributes The original attributes.
     * @param array $attributesPrepared The prepared attributes.
     * 
     * @return array The extra data determined.
     */
    private function determineExtraData(array $attributes, array $preparedAttributes): array
    {
        $extraData = [];
        foreach ($attributes as $attribute => $attributeValue) {
            if (!array_key_exists($attribute, $preparedAttributes)) {
                $extraData[$attribute] = $attributeValue;
            }
        }

        return $extraData;
    }
}