<?php

namespace App\DataResultObjects\Process\Composed;

class ReadBpmnUseCaseDRO
{
    public function __construct(
        public readonly array $lanes,
        public readonly array $events,
        public readonly array $activities,
        public readonly array $gateways
    ) { }
}