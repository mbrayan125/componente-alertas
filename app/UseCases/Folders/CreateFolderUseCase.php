<?php

namespace App\UseCases\Folders;

use App\UseCases\Folders\Contracts\CreateFolderUseCaseInterface;
use Exception;

class CreateFolderUseCase implements CreateFolderUseCaseInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(string $folderPath): void
    {
        if (file_exists($folderPath) && is_dir($folderPath)) {
            return;
        }

        if (file_exists($folderPath) && is_file($folderPath)) {
            throw new Exception('Ya existe un archivo con el mismo nombre en la ruta de destino');
        }

        mkdir($folderPath, 0777, true);
    }
}