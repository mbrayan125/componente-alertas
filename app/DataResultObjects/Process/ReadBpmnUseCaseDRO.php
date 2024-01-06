<?php

namespace App\DataResultObjects\Process;

use App\DataResultObjects\Generic\BaseResultDRO;
use App\DataResultObjects\Process\Composed\ReadBpmnUseCaseDRO as Composed;

/**
 * @property Composed $data
 */
class ReadBpmnUseCaseDRO extends BaseResultDRO
{
    /**
     * @inheritDoc     
     */
    protected function isValidDataType(mixed $data): bool
    {
        return $data instanceof Composed;
    }

    /**
     * @inheritDoc
     */
    protected static function generateComposedData(mixed ...$data): Composed
    {
        $lanes      = $data[0] ?? [];
        $events     = $data[1] ?? [];
        $activities = $data[2] ?? [];
        $gateways   = $data[3] ?? [];

        return new Composed($lanes, $events, $activities, $gateways);
    }
}