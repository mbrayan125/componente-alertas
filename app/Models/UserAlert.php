<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class UserAlert extends AbstractModel
{
    protected $fillable = [
        'process_instance_id',
        'type',
        'visual_representation',
        'color',
        'message',
        'buttons'
    ];

    public function processInstance()
    {
        return $this->belongsTo(ProcessInstance::class);
    }
}