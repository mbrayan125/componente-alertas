<?php

namespace App\UseCases\Models\Contracts;

use App\Models\Abstracts\AbstractModel;

interface CreateModelUseCaseInterface
{
    public function __invoke(array $attributes): AbstractModel;
}