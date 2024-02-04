<?php

namespace Tests;

use App\UseCases\Generic\Contracts\VerbFromInfinitiveToParticipleUseCaseInterface;
use Tests\TestCase;

class VerbFromInfinitiveToParticipleUseCaseTest extends TestCase
{
    /**
     * Test case for converting regular verbs from infinitive to participle form.
     *
     * This test case verifies that the VerbFromInfinitiveToParticipleUseCase correctly converts regular verbs
     * from their infinitive form to their participle form.
     *
     * @return void
     */
    public function test_regular_verbs()
    {
        $verbs = [
            'correr'   => 'corrido',
            'comer'    => 'comido',
            'vivir'    => 'vivido',
            'comer'    => 'comido',
        ];

        $verbFromInfinitiveToParticiple = app()->make(VerbFromInfinitiveToParticipleUseCaseInterface::class);
        foreach ($verbs as $infinitive => $participle) {
            $this->assertEquals($participle, $verbFromInfinitiveToParticiple($infinitive));
        }
    }
}