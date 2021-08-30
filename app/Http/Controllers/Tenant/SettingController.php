<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function listBanks()
    {
        return view('tenant.settings.list_banks');
    }

    public function listAccountBanks()
    {
        return view('tenant.settings.list_account_banks');
    }

    public function listCurrencies()
    {
        return view('tenant.settings.list_currencies');
    }

    public function listCards()
    {
        return view('tenant.settings.list_cards');
    }

    public function listPlatforms()
    {
        return view('tenant.settings.list_platforms');
    }

    public function listAttributes()
    {
        return view('tenant.settings.list_attributes');
    }

    public function listDetractions()
    {
        return view('tenant.settings.list_detractions');
    }

    public function listUnits()
    {
        return view('tenant.settings.list_units');
    }

    public function listPaymentMethods()
    {
        return view('tenant.settings.list_payment_methods');
    }

    public function listIncomes()
    {
        return view('tenant.settings.list_incomes');
    }

    public function listPayments()
    {
        return view('tenant.settings.list_payments');
    }

    public function listVouchersType()
    {
        return view('tenant.settings.list_vouchers_type');
    }

    public function listReports()
    {
        return view('tenant.reports.list');
    }

    public function indexSettings()
    {
        return view('tenant.settings.list_settings');
    }
}
