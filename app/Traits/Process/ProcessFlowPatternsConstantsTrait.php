<?php

namespace App\Traits\Process;

trait ProcessFlowPatternsConstantsTrait
{
    private const SEQUENCE_PATTERN        = 'sequence_pattern';
    private const EXCLUSIVE_SPLIT_PATTERN = 'exclusive_split_pattern';
    private const EXCLUSIVE_JOIN_PATTERN  = 'exclusive_join_pattern';
    private const PARALLEL_SPLIT_PATTERN  = 'parallel_split_pattern';
    private const PARALLEL_JOIN_PATTERN   = 'parallel_join_pattern';
}