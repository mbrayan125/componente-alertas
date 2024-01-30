<?php

namespace App\UseCases\Models\Contracts;

use App\Models\Abstracts\AbstractModel;

interface CreateUpdateModelUseCaseInterface
{
    public function __invoke(array $attributes, AbstractModel $current = null): AbstractModel;
}