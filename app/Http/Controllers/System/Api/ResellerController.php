<?php

namespace App\Http\Controllers\System\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use App\Http\Resources\System\ClientCollection;
use App\Http\Requests\System\ClientRequest;
use Hyn\Tenancy\Environment;
use App\Models\System\Client;
use App\Models\System\Plan;
use App\Models\System\Configuration;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use App\Models\System\Configuration;

class ResellerController extends Controller
{

    public function resellerDetail()
    {

        $records = Client::latest()->get();

        foreach ($records as &$row) {
            $tenancy = app(Environment::class);
            $tenancy->tenant($row->hostname->website);
            $row->count_doc = DB::connection('tenant')->table('documents')->count();
            $row->count_user = DB::connection('tenant')->table('users')->count();
        }

        return new ClientCollection($records);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'No Autorizado'
            ];
        }

        $user = $request->user();
        return [
            'success' => true,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->api_token,
        ];
    }

    public function lockedAdmin(Request $request)
    {
        // dd($request->locked_admin);
        
        $configuration = Configuration::first();
        $configuration->locked_admin = $request->locked_admin;
        $configuration->save();

        
        $clients = Client::get();

        foreach ($clients as $client) {

            $client->locked_tenant = $configuration->locked_admin;
            $client->save();

            $tenancy = app(Environment::class);
            $tenancy->tenant($client->hostname->website);
            DB::connection('tenant')->table('configurations')->where('id', 1)->update(['locked_tenant' => $client->locked_tenant]);
 
        } 

        return [
            'success' => true,
            'message' => ($configuration->locked_admin) ? 'Cuenta bloqueada' : 'Cuenta desbloqueada'
        ];

    }


    // public function lockedAdmin(Request $request)
    // {

    //     $configuration = Configuration::first();
    //     $configuration->locked_admin = $request->locked_admin;
    //     $configuration->save();


    //     $clients = Client::get();

    //     foreach ($clients as $client) {

    //         $client->locked_tenant = $configuration->locked_admin;
    //         $client->save();

    //         $tenancy = app(Environment::class);
    //         $tenancy->tenant($client->hostname->website);
    //         DB::connection('tenant')->table('configurations')->where('id', 1)->update(['locked_tenant' => $client->locked_tenant]);

    //     }

    //     return [
    //         'success' => true,
    //         'message' => ($configuration->locked_admin) ? 'Cuenta bloqueada' : 'Cuenta desbloqueada'
    //     ];

    // }


}
