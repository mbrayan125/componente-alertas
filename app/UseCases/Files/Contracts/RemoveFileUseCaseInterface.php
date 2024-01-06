<?php

namespace App\UseCases\Files\Contracts;

interface RemoveFileUseCaseInterface
{
    /**
     * Removes a file from a path.
     * 
     * @param string $path The path from where the file will be removed.
     * 
     * @return void
     */
    public function __invoke(string $path): void;
}