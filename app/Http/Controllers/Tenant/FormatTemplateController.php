<?php
namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use App\Models\Tenant\FormatTemplate;
use App\Http\Resources\Tenant\FormatTemplateCollection;

class FormatTemplateController extends Controller
{
    public function records() {

        $formats = FormatTemplate::all();

        return new FormatTemplateCollection($formats);
    }
}
