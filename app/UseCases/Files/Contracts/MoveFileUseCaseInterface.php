<?php

namespace App\UseCases\Files\Contracts;

interface MoveFileUseCaseInterface
{
    /**
     * Moves a file from a path to another.
     * 
     * @param string $fromPath The path from where the file will be moved.
     * @param string $toPath The path to where the file will be moved.
     * 
     * @return void
     */
    public function __invoke(string $fromPath, string $toPath): void;
}