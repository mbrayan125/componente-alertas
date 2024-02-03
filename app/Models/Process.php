<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\Contracts\ModelPublicMapeableInterface;

class Process extends AbstractModel implements ModelPublicMapeableInterface
{
    protected $fillable = [
        'target_system_id',
        'name_subject',
        'name_verb',
        'name_complement',
        'token',
        'bpmn_filepath',
        'version',
        'risky_execution',
        'idempotent_execution',
    ];

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'process_id');
    }

    public function elements()
    {
        return $this->hasMany(ProcessElement::class, 'process_id');
    }

    public function targetSystem()
    {
        return $this->belongsTo(TargetSystem::class);
    }

    /**
     * Returns the full name of the process.
     *
     * @return string The full name of the process.
     */
    public function getFullName()
    {
        return sprintf('%s %s %s', $this->name_verb, $this->name_subject, $this->name_complement);
    }

    public function getPublicMapeableData(): array
    {
        $elementsArray = $userRolesArray = [];
        foreach ($this->elements as $element) {
            $elementsArray[] = $element->getPublicMapeableData();
        }
        foreach ($this->userRoles as $userRole) {
            $userRolesArray[] = $userRole->getPublicMapeableData();
        }
        return [
            'name'          => $this->getFullName(),
            'target_system' => $this->targetSystem->name,
            'process_token' => $this->token,
            'version'       => $this->version,
            'created_at'    => $this->created_at,
            'elements'      => $elementsArray,
            'user_roles'    => $userRolesArray
        ];
    }
}
