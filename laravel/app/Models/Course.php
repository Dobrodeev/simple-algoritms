<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['symbol', 'price'];

    /**
     * CSS class for highlighting a currency pair by its quote currency:
     * BTC — green, ETH — yellow, USDT — blue.
     */
    public static function highlightClass(string $symbol): string
    {
        return match (true) {
            str_ends_with($symbol, 'USDT') => 'pair-usdt',
            str_ends_with($symbol, 'BTC') => 'pair-btc',
            str_ends_with($symbol, 'ETH') => 'pair-eth',
            default => '',
        };
    }
}
