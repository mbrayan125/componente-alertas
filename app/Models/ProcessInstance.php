<?php

namespace App\Models;
use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class ProcessInstance extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $fillable = [
        'target_system_id',
        'process_id',
        'current_element_id',
        'token'
    ];

    public function targetSystem()
    {
        return $this->belongsTo(TargetSystem::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function currentElement()
    {
        return $this->belongsTo(ProcessElement::class, 'current_element_id');
    }

    public function getPublicMapeableData(): array
    {
        return [
            'process_instance_token' => $this->token,
            'created_at'             => $this->created_at,
            'current_status' => [
                'current_element'      => $this->currentElement->bpmn_id,
                'current_element_name' => $this->currentElement->name,
            ],
            'instance_history'       => []
        ];
    }
}
