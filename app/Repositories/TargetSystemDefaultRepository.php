<?php

namespace App\Repositories;

use App\Exceptions\Model\ModelNotFoundException;
use App\Models\TargetSystem;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;

class TargetSystemDefaultRepository extends ModelRepositoryAbstract implements TargetSystemRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return TargetSystem::class;
    }

    /**
     * @inheritDoc
     */
    public function findByToken(string $token): ?TargetSystem
    {
        $searchParams = ['token' => $token];
        if (!$targetSystem = $this->findOneBy($searchParams)) {
            throw new ModelNotFoundException('sistema objetivo', $searchParams);
        }

        return $targetSystem;
    }
}