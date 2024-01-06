<?php

namespace App\UseCases\Files;

use App\UseCases\Folders\Contracts\CreateFolderUseCaseInterface;
use App\UseCases\Files\Contracts\MoveFileUseCaseInterface;
use Exception;

class MoveFileUseCase implements MoveFileUseCaseInterface
{
    public function __construct(
        private readonly CreateFolderUseCaseInterface $createFolderUseCase
    ) { }

    /**
     * @inheritDoc
     */
    public function __invoke(string $fromPath, string $toPath): void
    {
        if (!file_exists($fromPath)) {
            throw new Exception('El archivo no existe');
        }

        if (file_exists($toPath)) {
            throw new Exception('Ya existe un archivo con el mismo nombre en la ruta de destino');
        }

        $pathInfo = pathinfo($toPath);
        $toDirectory = $pathInfo['dirname'];
        if (!is_dir($toDirectory)) {
            ($this->createFolderUseCase)($toDirectory);
        }

        rename($fromPath, $toPath);
    }
}