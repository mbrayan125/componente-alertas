<?php

namespace App\Traits\Process;

trait ProcessFlowPatternsConstantsTrait
{
    protected const SEQUENCE_PATTERN        = 'sequence_pattern';
    protected const EXCLUSIVE_SPLIT_PATTERN = 'exclusive_split_pattern';
    protected const EXCLUSIVE_JOIN_PATTERN  = 'exclusive_join_pattern';
    protected const PARALLEL_SPLIT_PATTERN  = 'parallel_split_pattern';
    protected const PARALLEL_JOIN_PATTERN   = 'parallel_join_pattern';
}