<?php

namespace App\Traits\Exception;

trait InstanceOfExceptionTrait
{
    public function isInstanceOfException($exception, $className)
    {
        return $exception instanceof $className;
    }

    public function isInstanceOfOneOfExceptions($exception, array $classNames)
    {
        foreach ($classNames as $className) {
            if ($this->isInstanceOfException($exception, $className)) {
                return true;
            }
        }

        return false;
    }
}
