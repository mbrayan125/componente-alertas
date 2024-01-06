<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;

class UserRole extends AbstractModel
{
    protected $fillable = [
        'process_id',
        'name',
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}