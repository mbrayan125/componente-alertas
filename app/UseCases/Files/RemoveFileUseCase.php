<?php

namespace App\UseCases\Files;

use App\UseCases\Files\Contracts\RemoveFileUseCaseInterface;
use Exception;

class RemoveFileUseCase implements RemoveFileUseCaseInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception('El archivo no existe');
        }

        if (!is_file($path)) {
            throw new Exception('El path no corresponde a un archivo');
        }

        unlink($path);
    }
}