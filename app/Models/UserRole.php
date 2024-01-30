<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class UserRole extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $fillable = [
        'process_id',
        'name',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function getPublicMapeableData(): array
    {
        return [
            'name' => $this->name
        ];
    }
}