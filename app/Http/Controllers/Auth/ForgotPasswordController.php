<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tenant\Company;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\System\Configuration as SystemConfiguration;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $config = SystemConfiguration::first();
        if (! $config->use_login_global) {
            $config = Configuration::first();
        }
        $useLoginGlobal = $config->use_login_global;
        $company = Company::first();
        $login = $config->login;
        return view('tenant.auth.passwords.email', compact('company', 'login', 'useLoginGlobal'));
    }
}
