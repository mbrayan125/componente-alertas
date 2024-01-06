<?php

namespace App\Models\Contracts;

use App\Models\Abstracts\AbstractModel;

interface ModelInterface
{
    public function fillData(array $data): AbstractModel;
}