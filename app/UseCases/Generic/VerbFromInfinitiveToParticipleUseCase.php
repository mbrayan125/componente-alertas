<?php

namespace App\UseCases\Generic;

use App\UseCases\Generic\Contracts\VerbFromInfinitiveToParticipleUseCaseInterface;

class VerbFromInfinitiveToParticipleUseCase implements VerbFromInfinitiveToParticipleUseCaseInterface
{
    public function __invoke(string $infinitiveVerb): string
    {
        $spanishEndings = array('ar' => 'ado', 'er' => 'ido', 'ir' => 'ido');
        $conjugation = '';
        foreach ($spanishEndings as $ending => $participle) {
            if (substr($infinitiveVerb, -strlen($ending)) === $ending) {
                $conjugation = $ending;
                break;
            }
        }
        if ($conjugation) {
            return substr($infinitiveVerb, 0, -strlen($conjugation)) . $participle;
        }

        return $infinitiveVerb;
    }
}