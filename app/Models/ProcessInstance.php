<?php

namespace App\Models;
use App\Models\Abstracts\AbstractModel;

class ProcessInstance extends AbstractModel
{
    protected $fillable = [
        'target_system_id',
        'process_id',
        'unique_identifier'
    ];

    public function targetSystem()
    {
        return $this->belongsTo(TargetSystem::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
