<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class UserAlert extends AbstractModel
{
    protected $fillable = [
        'process_instance_history_id',
        'type',
        'visual_representation',
        'color',
        'title',
        'message',
        'icon',
        'actions',
        'alert_moment'
    ];

    protected $casts = [
        'actions' => 'array'
    ];

    public function processInstance()
    {
        return $this->belongsTo(ProcessInstanceHistory::class);
    }
}