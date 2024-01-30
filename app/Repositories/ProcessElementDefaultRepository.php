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
}