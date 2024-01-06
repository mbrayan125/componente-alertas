<?php

namespace App\DataResultObjects\Generic;

class ResultDRO extends BaseResultDRO
{
    /**
     * @inheritDoc
     */
    protected function isValidDataType(mixed $data): bool
    {
        return is_array($data);
    }

    protected static function generateComposedData(mixed ...$data): array
    {
        return $data[0];
    }
}