<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class TargetSystem extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $fillable = [
        'name',
        'token'
    ];

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function getPublicMapeableData(): array
    {
        $processes = [];
        foreach ($this->processes as $process) {
            $processes[] = [
                'process_token' => $process->token,
                'process_name'  => $process->getFullName()
            ];
        }

        return [
            'name'                => $this->name,
            'target_system_token' => $this->token,
            'created_at'          => $this->created_at,
            'processes'           => $processes
        ];
    }
}
