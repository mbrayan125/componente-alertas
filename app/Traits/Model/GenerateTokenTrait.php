<?php

namespace App\Traits\Model;

trait GenerateTokenTrait
{
    /**
     * Generates a token for the model.
     * 
     * @return string The generated token.
     */
    public function generateToken(): string
    {
        $uniqid = uniqid('', true);
        $token = md5($uniqid);
        return $token;
    }
}