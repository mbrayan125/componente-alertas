<?php

namespace App\Repositories;

use App\Exceptions\Model\ModelNotFoundException;
use App\Models\ProcessInstance;
use App\Models\TargetSystem;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;

class ProcessInstanceDefaultRepository extends ModelRepositoryAbstract implements ProcessInstanceRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return ProcessInstance::class;
    }

    /**
     * @inheritDoc
     */
    public function findByTargetSystemAndInstanceToken(TargetSystem $targetSystem, string $instanceToken): ?ProcessInstance
    {
        $searchParams = [
            'target_system_id' => $targetSystem->id,
            'token'            => $instanceToken
        ];

        if (!$processInstance = $this->findOneBy($searchParams)) {
            throw new ModelNotFoundException('instancia de proceso', $searchParams);
        }

        return $processInstance;
    }
}