<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

class AdvancedController extends Controller
{
    public function index() {
        return view('tenant.advanced.index');
    }
}