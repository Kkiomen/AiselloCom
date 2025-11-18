<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CostCalculator;
use Tests\TestCase;

class CostCalculatorTest extends TestCase
{
    /**
     * Test kalkulacji kosztów dla GPT-4o-mini.
     */
    public function test_calculate_cost_for_gpt4o_mini(): void
    {
        $cost = CostCalculator::calculate(1000, 500, 'gpt-4o-mini');

        // 1000 input tokens * $0.00015 / 1000 = $0.00015
        // 500 output tokens * $0.0006 / 1000 = $0.0003
        // Total = $0.00045
        $this->assertEquals(0.0005, $cost); // Zaokrąglone do 4 miejsc
    }

    /**
     * Test formatowania kosztów.
     */
    public function test_format_cost(): void
    {
        $formatted = CostCalculator::format(0.1234, 'USD');
        $this->assertEquals('0.1234 USD', $formatted);
    }
}
