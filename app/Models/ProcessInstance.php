<?php

namespace App\Models;
use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class ProcessInstance extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $fillable = [
        'target_system_id',
        'process_id',
        'current_history_id',
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

    public function history()
    {
        return $this->hasMany(ProcessInstanceHistory::class);
    }

    public function currentHistory()
    {
        return $this->belongsTo(ProcessInstanceHistory::class, 'current_history_id');
    }

    public function currentElement(): ?ProcessElement
    {
        return $this->currentHistory?->processElement;
    }

    /**
     * @inheritDoc
     */
    public function getPublicMapeableData(): array
    {
        $instanceHistoryArray = [];
        foreach ($this->history as $history) {
            $instanceHistoryArray[] = $history->getPublicMapeableData();
        }
        $currentStatus = [];
        if ($this->currentHistory) {
            $currentStatus = [
                'current_element'      => $this->currentHistory->processElement->bpmn_id,
                'current_element_name' => $this->currentHistory->processElement->name,
            ];
        }

        return [
            'process_token'          => $this->process->token,
            'process_instance_token' => $this->token,
            'created_at'             => $this->created_at,
            'current_status'         => $currentStatus,
            'instance_history'       => $instanceHistoryArray
        ];
    }
}
