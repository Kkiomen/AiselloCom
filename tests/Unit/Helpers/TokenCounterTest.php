<?php

namespace Tests\Unit\Helpers;

use App\Helpers\TokenCounter;
use Tests\TestCase;

class TokenCounterTest extends TestCase
{
    /**
     * Test szacowania tokenów dla tekstu polskiego.
     */
    public function test_estimate_tokens_polish(): void
    {
        $text = 'To jest testowy tekst w języku polskim.';
        $tokens = TokenCounter::estimate($text, 'pl');

        // ~40 znaków / 5 = ~8 tokenów
        $this->assertGreaterThan(0, $tokens);
        $this->assertLessThan(20, $tokens);
    }

    /**
     * Test szacowania tokenów dla pustego tekstu.
     */
    public function test_estimate_tokens_empty(): void
    {
        $tokens = TokenCounter::estimate('');
        $this->assertEquals(0, $tokens);
    }
}
