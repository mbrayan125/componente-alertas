<?php

namespace App\UseCases\Folders\Contracts;

interface CreateFolderUseCaseInterface
{
    /**
     * Creates a folder.
     * 
     * @param string $folderPath The path to the folder to create.
     * 
     * @return void
     */
    public function __invoke(string $folderPath): void;
}