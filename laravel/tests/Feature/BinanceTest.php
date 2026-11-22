<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BinanceTest extends TestCase
{
    public function test_shows_prices_from_api(): void
    {
        Http::fake(['*/api/v3/ticker/price' => Http::response([
            ['symbol' => 'BNBETH', 'price' => '0.19000000'],
        ])]);

        $this->get('/binance')->assertOk()->assertSee('BNBETH')->assertSee('class="pair-eth"', false);
    }

    public function test_shows_error_when_api_fails(): void
    {
        Http::fake(['*' => Http::response(null, 500)]);

        $this->get('/binance')->assertOk()->assertSee('Не удалось получить курсы');
    }
}
