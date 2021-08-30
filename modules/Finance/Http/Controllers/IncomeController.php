<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Finance\Models\Income;
use Modules\Finance\Models\IncomeReason;
use Modules\Finance\Models\IncomePayment;
use Modules\Finance\Models\IncomeType;
use Modules\Finance\Models\IncomeMethodType;
use Modules\Finance\Models\IncomeItem;
use Modules\Finance\Http\Resources\IncomeCollection;
use Modules\Finance\Http\Resources\IncomeResource;
use Modules\Finance\Http\Requests\IncomeRequest;
use Illuminate\Support\Str;
use App\Models\Tenant\Person;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use Modules\Finance\Traits\FinanceTrait; 

class IncomeController extends Controller
{

    use FinanceTrait;

    public function index()
    {
        return view('finance::income.index');
    }


    public function create()
    {
        return view('finance::income.form');
    }

    public function columns()
    {
        return [
            'number' => 'Número',
            'date_of_issue' => 'Fecha de emisión',
        ];
    }


    public function records(Request $request)
    {
        $records = Income::where($request->column, 'like', "%{$request->value}%")
                            ->whereTypeUser()
                            ->latest();

        return new IncomeCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function tables()
    {
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $currency_types = CurrencyType::whereActive()->get();
        $income_types = IncomeType::get();
        $payment_method_types = PaymentMethodType::all();
        $income_reasons = IncomeReason::all();
        $payment_destinations = $this->getPaymentDestinations();

        return compact('establishment','currency_types', 'income_types', 'payment_method_types', 'income_reasons', 'payment_destinations');
    }



    public function record($id)
    {
        $record = new IncomeResource(Income::findOrFail($id));

        return $record;
    }

    public function store(IncomeRequest $request)
    {

        $data = self::merge_inputs($request);

        $income = DB::connection('tenant')->transaction(function () use ($data) {

            $doc = Income::create($data);

            foreach ($data['items'] as $row)
            {
                $doc->items()->create($row);
            }

            foreach ($data['payments'] as $row)
            {
                $record_payment = $doc->payments()->create($row);
                $this->createGlobalPayment($record_payment, $row);
            }

            return $doc;
        });

        return [
            'success' => true,
            'data' => [
                'id' => $income->id,
            ],
        ];
    }

    public static function merge_inputs($inputs)
    {

        $company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'number' => $inputs['id'] ? $inputs['number'] : self::newNumber($company->soap_type_id),
            'state_type_id' => '05',
            'soap_type_id' => $company->soap_type_id,
            'external_id' => Str::uuid()->toString(),
        ];

        $inputs->merge($values);

        return $inputs->all();
    }

    private static function newNumber($soap_type_id){

        $number = Income::select('number')
                            ->where('soap_type_id', $soap_type_id)
                            ->max('number');

        return ($number) ? (int)$number+1 : 1;

    }

    public function table($table)
    {
        switch ($table) {
            case 'suppliers':

                $suppliers = Person::whereType('suppliers')->orderBy('name')->get()->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->number.' - '.$row->name,
                        'name' => $row->name,
                        'number' => $row->number,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $suppliers;

                break;
            default:

                return [];

                break;
        }
    }

    public function voided($id)
    {

        $income = Income::findOrFail($id);
        $income->state_type_id = 11;
        $income->save();

        return [
            'success' => true, 
            'message' => 'Ingreso anulado exitosamente',
        ];
    }


}
