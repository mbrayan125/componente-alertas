<?php 

namespace App\Exceptions\General;

use App\Traits\Response\ResponseConstantsTrait;
use Exception;

class DomainException extends Exception {

    use ResponseConstantsTrait;

    public function __construct(
        public readonly string $mainMessage,
        public readonly array $errors,
        public readonly array $warnings,
        public readonly int $httpCode = self::HTTP_BAD_REQUEST
    ) { }
}
