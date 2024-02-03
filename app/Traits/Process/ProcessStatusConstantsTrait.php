<?php

namespace App\Traits\Process;

trait ProcessStatusConstantsTrait
{
    private const CENTRAL_ACTIVITY         = 'centralActivity';
    private const CONTROL_GATEWAY_ACTIVITY = 'controlGatewayActivity';
    private const FINAL_ACTIVITY           = 'finalActivity';
    private const PHARALLEL_ACTIVITY       = 'parallelActivity';
    private const SPLIT_GATEWAY            = 'splitGateway';
    private const JOIN_GATEWAY             = 'joinGateway';
    private const PROCESS_START            = 'processStart';
    private const PROCESS_END              = 'processEnd';
}