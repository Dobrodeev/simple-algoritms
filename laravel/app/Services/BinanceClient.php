<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BinanceClient
{
    public function __construct(private readonly string $baseUrl)
    {
    }

    /**
     * Current prices of all symbols.
     *
     * @return array<int, array{symbol: string, price: string}>
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function prices(): array
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout(10)
            ->get('/api/v3/ticker/price')
            ->throw()
            ->json();
    }
}
