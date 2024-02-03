<?php

namespace App\UseCases\Process;

use App\Models\Process;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Traits\Process\ElementsTypesConstantsTrait;
use App\Traits\Process\ProcessFlowPatternsConstantsTrait;
use App\Traits\Process\ProcessStatusConstantsTrait;
use App\Traits\ProcessElement\ProcessElementAssertsTrait;
use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;

class CheckFlowPatternBpmnProcessUseCase implements CheckFlowPatternBpmnProcessUseCaseInterface
{
    use ElementsTypesConstantsTrait;
    use ProcessFlowPatternsConstantsTrait;
    use ProcessStatusConstantsTrait;
    use ProcessElementAssertsTrait;

    public function __construct(
        private readonly ProcessElementRepositoryInterface $processElementRepository
    ) { }

    /**
     * @inheritDoc
     */
    public function __invoke(Process $process): array
    {
        return $this->getFlowPatterns($process);
    }

    /**
     * Retrieves the flow patterns of a given process.
     *
     * @param Process $process The process to retrieve the flow patterns from.
     * @return array The array of flow patterns.
     */
    private function getFlowPatterns(Process $process): array
    {
        // Retrieve all gateways of the process
        $gateways = $this->processElementRepository->getElementsByTypeAndProcess($process, self::GATEWAY);

        // If there are no gateways, return the default sequence pattern
        if (count($gateways) === 0) {
            return [self::SEQUENCE_PATTERN];
        }

        $flowPatterns = [];
        foreach ($gateways as $gateway) {
            $isJoinGateway = $this->isJoinGateway($gateway);
            $isSplitGateway = $this->isSplitGateway($gateway);

            // Check if the gateway is an exclusive join gateway and add the pattern if it's not already in the array
            if ($isJoinGateway && $gateway->subtype === 'exclusiveGateway' && !in_array(self::EXCLUSIVE_JOIN_PATTERN, $flowPatterns)) {
                $flowPatterns[] = self::EXCLUSIVE_JOIN_PATTERN;
            }

            // Check if the gateway is an exclusive split gateway and add the pattern if it's not already in the array
            if ($isSplitGateway && $gateway->subtype === 'exclusiveGateway' && !in_array(self::EXCLUSIVE_SPLIT_PATTERN, $flowPatterns)) {
                $flowPatterns[] = self::EXCLUSIVE_SPLIT_PATTERN;
            }

            // Check if the gateway is a parallel join gateway and add the pattern if it's not already in the array
            if ($isJoinGateway && $gateway->subtype === 'parallelGateway' && !in_array(self::PARALLEL_JOIN_PATTERN, $flowPatterns)) {
                $flowPatterns[] = self::PARALLEL_JOIN_PATTERN;
            }

            // Check if the gateway is a parallel split gateway and add the pattern if it's not already in the array
            if ($isSplitGateway && $gateway->subtype === 'parallelGateway' && !in_array(self::PARALLEL_SPLIT_PATTERN, $flowPatterns)) {
                $flowPatterns[] = self::PARALLEL_SPLIT_PATTERN;
            }
        }

        return $flowPatterns;
    }
}