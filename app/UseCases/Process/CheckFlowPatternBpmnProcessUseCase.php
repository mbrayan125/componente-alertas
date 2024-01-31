<?php

namespace App\UseCases\Process;

use App\Models\Process;
use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Traits\Process\ElementsTypesConstantsTrait;
use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;

class CheckFlowPatternBpmnProcessUseCase implements CheckFlowPatternBpmnProcessUseCaseInterface
{
    use ElementsTypesConstantsTrait;

    public function __construct(
        private readonly ProcessElementRepositoryInterface $processElementRepository
    ) { }

    public function __invoke(Process $process): Result
    {
        $evaluatedElement = $this->processElementRepository->getStartEventByProcess($process);

        $activities = $this->processElementRepository->getElementsByTypeAndProcess($process, self::ACTIVITY);
        $gateways = $this->processElementRepository->getElementsByTypeAndProcess($process, self::GATEWAY);

        return Result::createSuccess([]);
    }
}