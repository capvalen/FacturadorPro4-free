<?php
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\ClientPaymentRequest;
use App\Http\Requests\System\DocumentRequest;
use App\Http\Resources\System\ClientPaymentCollection;
use App\Models\System\Client;
use App\Models\System\ClientPayment;
use App\Models\System\PaymentMethodType;
use App\Models\System\CardBrand;

class AccountStatusController extends Controller
{
    public function records($client_id)
    {
        $records = ClientPayment::where('client_id', $client_id)->get();

        return new ClientPaymentCollection($records);
    }

    public function tables()
    {
        return [
            'payment_method_types' => PaymentMethodType::all(),
            'card_brands' => CardBrand::all()
        ];
    }

    public function client($client_id)
    {
        $client = Client::find($client_id);

        $total_due = collect($client->payments)->where('state',false)->sum('payment');
        $total_paid = collect($client->payments)->where('state',true)->sum('payment');
        $total = collect($client->payments)->sum('payment');
        $total_difference = round($total - $total_paid, 2);
        $image_url = ($total_due>0) ? asset('/logo/sad.png') : asset('/logo/happy.png');

        return [
            'totals' => [
                'total_due' => $total_due,
                'total_paid' => $total_paid,
                'total' => $total,
                'total_difference' => $total_difference,
            ],
            'client' => $client, 
            'image_url' => $image_url
        ];

    } 


}
