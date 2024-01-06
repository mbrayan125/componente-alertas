<?php

namespace App\Models\Abstracts;

use App\Models\Contracts\ModelInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model implements ModelInterface
{
    public function fillData(array $data): AbstractModel
    {
        $this->fill($data);
        return $this;
    }
}