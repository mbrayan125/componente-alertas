<?php

namespace App\Repositories;

use App\Exceptions\Model\ModelNotFoundException;
use App\Models\Process;
use App\Models\TargetSystem;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessRepositoryInterface;

class ProcessDefaultRepository extends ModelRepositoryAbstract implements ProcessRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return Process::class;
    }
    
    /**
     * @inheritDoc
     */
    public function findByTargetSystemAndToken(TargetSystem $targetSystem, string $processToken): ?Process
    {
        $searchParams = [
            'target_system_id' => $targetSystem->id,
            'token'            => $processToken
        ];
        if (!$process = $this->findOneBy($searchParams)) {
            $searchParams = [
                'target_system' => $targetSystem->name,
                'process_token' => $processToken
            ];
            throw new ModelNotFoundException('proceso', $searchParams);
        }

        return $process;
    }
}