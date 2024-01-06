<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class Process extends AbstractModel
{
    protected $fillable = [
        'target_system_id',
        'version'
    ];

    public function targetSystem()
    {
        return $this->belongsTo(TargetSystem::class);
    }
}
