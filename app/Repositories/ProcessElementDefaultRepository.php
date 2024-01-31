<?php

namespace App\Repositories;

use App\Exceptions\Model\ModelNotFoundException;
use App\Models\Process;
use App\Models\ProcessElement;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Traits\Process\ElementsTypesConstantsTrait;

class ProcessElementDefaultRepository extends ModelRepositoryAbstract implements ProcessElementRepositoryInterface
{
    use ElementsTypesConstantsTrait;

    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return ProcessElement::class;
    }

    /**
     * @inheritDoc
     */
    public function getStartEventByProcess(Process $process): ?ProcessElement
    {
        $searchParams = [
            'process_id' => $process->id,
            'type'       => self::EVENT,
            'subtype'    => 'startEvent'
        ];

        if (!$startEvent = $this->findOneBy($searchParams)) {
            throw new ModelNotFoundException('elemento de proceso', $searchParams);
        }

        return $startEvent;
    }

    /**
     * Get a process element by its BPMN ID and process.
     *
     * @param Process $process The process object.
     * @param string $bpmnId The BPMN ID of the process element.
     * @return ProcessElement|null The process element if found, null otherwise.
     * 
     * @throws ModelNotFoundException If the process element is not found.
     */
    public function getByBpmnIdAndProcess(Process $process, string $bpmnId): ?ProcessElement
    {
        $searchParams = [
            'process_id' => $process->id,
            'bpmn_id'    => $bpmnId
        ];

        if (!$processElement = $this->findOneBy($searchParams)) {
            throw new ModelNotFoundException('elemento de proceso', $searchParams);
        }

        return $processElement;
    }

    /**
     * @inheritDoc
     */
    public function getElementsByTypeAndProcess(Process $process, string $type): array
    {
        $searchParams = [
            'process_id' => $process->id,
            'type'       => $type
        ];

        return $this->findBy($searchParams);
    }
}