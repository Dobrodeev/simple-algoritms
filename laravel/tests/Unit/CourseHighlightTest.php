<?php

namespace Tests\Unit;

use App\Models\Course;
use PHPUnit\Framework\TestCase;

class CourseHighlightTest extends TestCase
{
    public function test_highlight_class_by_quote_currency(): void
    {
        $this->assertSame('pair-btc', Course::highlightClass('ETHBTC'));
        $this->assertSame('pair-eth', Course::highlightClass('BNBETH'));
        $this->assertSame('pair-usdt', Course::highlightClass('BTCUSDT'));
        $this->assertSame('', Course::highlightClass('BTCBUSD'));
    }
}
