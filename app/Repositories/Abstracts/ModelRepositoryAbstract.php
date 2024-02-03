<?php

namespace App\Repositories\Abstracts;

use App\Models\Abstracts\AbstractModel;
use App\Repositories\Contracts\ModelRepositoryInterface;

abstract class ModelRepositoryAbstract implements ModelRepositoryInterface
{
    /**
     * Returns the fully qualified class name of the model.
     *
     * @return string The fully qualified class name of the model.
     */
    abstract protected function getModelClass(): string;

    /**
     * Saves a model.
     *
     * @param AbstractModel $model The model to be saved.
     * 
     * @return void
     */
    public function save(AbstractModel &$model)
    {
        $model->save();
    }

    /**
     * Busca un modelo por su ID.
     *
     * @param int $id El ID del modelo a buscar.
     * 
     * @return AbstractModel|null El modelo encontrado o null si no se encuentra.
     */
    public function findById($id): ?AbstractModel
    {
        return $this->getModelClass()::find($id);
    }

    /**
     * Find records by specified parameters.
     *
     * @param array $params The parameters to search for.
     * @param array $order The order in which to retrieve the records. Default is an empty array.
     * @param int|null $limit The maximum number of records to retrieve. Default is null (no limit).
     * 
     * @return array The array of matching records.
     */
    public function findBy(array $params, array $order = [], int $limit = null): array
    {
        $query = $this->getModelClass()::query();
        foreach ($params as $field => $value) {
            $query->where($field, $value);
        }
        foreach ($order as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        if ($limit) {
            $query->take($limit);
        }

        return $query->get()->all();
    }

    /**
     * Find one model by given parameters and order.
     *
     * @param array $params The parameters to search for.
     * @param array $order The order in which to retrieve the model.
     * 
     * @return AbstractModel|null The found model or null if not found.
     */
    public function findOneBy(array $params, array $order = []): ?AbstractModel
    {
        return $this->findBy($params, $order, 1)[0] ?? null;
    }

}