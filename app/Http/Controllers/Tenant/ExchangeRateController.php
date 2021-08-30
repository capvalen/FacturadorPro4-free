<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\ExchangeRateCollection;
use App\Models\Tenant\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function records()
    {
        $records = ExchangeRate::orderBy('date', 'desc')->get();

        return new ExchangeRateCollection($records);
    }

    public function store(Request $request)
    {
        $exchangeRates = $request->all();
        foreach ($exchangeRates as $exchangeRate) {
        	ExchangeRate::create($exchangeRate);
        }
        return [
            'success' => true,
            'message' => 'Tipos de cambio registrados con éxito'
        ];
    }
}