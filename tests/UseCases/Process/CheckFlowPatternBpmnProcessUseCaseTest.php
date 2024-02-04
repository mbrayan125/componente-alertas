<?php

namespace Tests;

use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;
use Tests\TestCase;

class CheckFlowPatternBpmnProcessUseCaseTest extends TestCase
{
    /**
     * Test case for the test_check_flow_pattern_use_case method case 1.
     */
    public function test_check_flow_pattern_sequence()
    {
        $processInfo = $this->createSequenceProcess();
        $checkFlowPatternUseCase = app()->make(CheckFlowPatternBpmnProcessUseCaseInterface::class);
        $flowPattern = $checkFlowPatternUseCase($processInfo['process']);
        $this->assertEquals($flowPattern, [
            self::SEQUENCE_PATTERN
        ]);
    }

    /**
     * Test case for the test_check_flow_pattern_use_case method case 2.
     */
    public function test_check_flow_pattern_exclusive()
    {
        $processInfo = $this->createExclusiveProcess();
        $checkFlowPatternUseCase = app()->make(CheckFlowPatternBpmnProcessUseCaseInterface::class);
        $flowPattern = $checkFlowPatternUseCase($processInfo['process']);
        $this->assertEquals($flowPattern, [
            self::EXCLUSIVE_SPLIT_PATTERN,
            self::EXCLUSIVE_JOIN_PATTERN
        ]);

    }

    /**
     * Test case for the test_check_flow_pattern_use_case method case 3.
     */
    public function test_check_flow_pattern_pharallel()
    {
        $processInfo = $this->createParallelProcess();
        $checkFlowPatternUseCase = app()->make(CheckFlowPatternBpmnProcessUseCaseInterface::class);
        $flowPattern = $checkFlowPatternUseCase($processInfo['process']);
        $this->assertEquals($flowPattern, [
            self::PARALLEL_SPLIT_PATTERN,
            self::PARALLEL_JOIN_PATTERN
        ]);

    }
}