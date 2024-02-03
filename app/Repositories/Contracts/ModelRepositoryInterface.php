<?php

namespace App\Repositories\Contracts;

use App\Models\Abstracts\AbstractModel;

interface ModelRepositoryInterface
{
    public function save(AbstractModel &$model);

    public function findById($id): ?AbstractModel;

    public function findBy(array $params, array $order = [], int $limit = null): array;

    public function findOneBy(array $params, array $order = []): ?AbstractModel;
}