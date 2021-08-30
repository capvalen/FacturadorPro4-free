<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Person;
use App\Models\Tenant\Company;

class ReportController extends Controller
{

    use ReportTrait;

    public function dataTablePerson($type, Request $request) {

        $persons = $this->getDataTablePerson($type, $request);

        return compact('persons');
    }

 
    public function dataTableItem(Request $request) {

        $items = $this->getDataTableItem($request);

        return compact('items');
    }
}
