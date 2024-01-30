<?php

namespace App\Models\Contracts;

interface ModelPublicMapeableInterface
{
    /**
     * Returns an array of public mappable data.
     *
     * @return array
     */
    public function getPublicMapeableData(): array;
}