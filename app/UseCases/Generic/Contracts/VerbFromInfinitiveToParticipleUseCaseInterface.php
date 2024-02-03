<?php

namespace App\UseCases\Generic\Contracts;

interface VerbFromInfinitiveToParticipleUseCaseInterface
{
    /**
     * Converts an infinitive verb to its participle form.
     *
     * @param string $infinitive The infinitive form of the verb.
     * 
     * @return string The participle form of the verb.
     */
    public function __invoke(string $infinitive): string;
}