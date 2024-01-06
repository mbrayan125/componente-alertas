<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class TargetSystem extends AbstractModel
{
    protected $fillable = [
        'name',
        'nickname',
        'process_path'
    ];
}
