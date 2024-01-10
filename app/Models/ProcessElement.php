<?php

namespace App\Models;

use App\Models\Abstracts\AbstractModel;
use App\Traits\Process\ElementsTypesConstantsTrait;

class ProcessElement extends AbstractModel
{
    use ElementsTypesConstantsTrait;

    protected $fillable = [
        'process_id',
        'user_role_id',
        'name',
        'type',
        'subtype'
    ];

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function userRole()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function relatedElements()
    {
        return $this
            ->belongsToMany(ProcessElement::class, 'process_elements_relations', 'process_element_id', 'referenced_process_element_id')
            ->withPivot('direction', 'name');
    }

    public function incomingElements()
    {
        return $this
            ->relatedElements()
            ->wherePivot('direction', 'input');
    }

    public function outgoingElements()
    {
        return $this
            ->relatedElements()
            ->wherePivot('direction', 'output');
    }

    public function addIncoming(ProcessElement $incomingElement, string $relationName = '')
    {
        $this->addRelatedElement($incomingElement, 'input', $relationName);
    }

    public function addOutgoing(ProcessElement $outgoingElement, string $relationName = '')
    {
        $this->addRelatedElement($outgoingElement, 'output', $relationName);
    }

    private function addRelatedElement(ProcessElement $relatedElement, string $direction, string $relationName = '')
    {
        $this->relatedElements()->attach($relatedElement, [
            'direction' => $direction,
            'name'      => $relationName
        ]);
    }

    public static function getAllElementsTypes(): array
    {
        return [
            self::EVENT,
            self::ACTIVITY,
            self::GATEWAY
        ];
    }
}
