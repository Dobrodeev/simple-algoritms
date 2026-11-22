<?php

namespace App\Http\Controllers;

use App\Services\BinanceClient;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class BinanceController extends Controller
{
    /** Current exchange rates from the Binance API. */
    public function index(BinanceClient $client): View
    {
        try {
            $prices = $client->prices();
            $error = null;
        } catch (Throwable $e) {
            Log::warning('Binance API request failed', ['error' => $e->getMessage()]);
            $prices = [];
            $error = 'Не удалось получить курсы с Binance. Попробуйте позже.';
        }

        return view('binance.index', compact('prices', 'error'));
    }
}
