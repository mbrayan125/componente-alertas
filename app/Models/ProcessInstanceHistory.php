<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class ProcessInstanceHistory extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $table = 'process_instances_history';

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

    public function getPublicMapeableData(): array
    {
        return [
            'element_id'   => $this->processElement->bpmn_id,
            'element_name' => $this->processElement->name,
            'created_at'   => $this->created_at
        ];
    }
}