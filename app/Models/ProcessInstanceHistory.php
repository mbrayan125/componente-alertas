<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class ProcessInstanceHistory extends AbstractModel
{
    protected $fillable = [
        'process_instance_id',
        'process_element_id',
        'history_previous_id'
    ];

    public function processInstance()
    {
        return $this->belongsTo(ProcessInstance::class);
    }

    public function processElement()
    {
        return $this->belongsTo(ProcessElement::class);
    }

    public function historyPrevious()
    {
        return $this->belongsTo(ProcessInstanceHistory::class, 'history_previous_id');
    }
}