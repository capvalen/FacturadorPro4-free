<?php

namespace App\Console\Commands;

use App\CoreFacturalo\Services\Extras\ValidateCpe2;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ValidateDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:documents {establishment_id?} {state_type_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consultar el estado de los documentos electrónicos';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $establishment_id = $this->argument('establishment_id');
        $state_type_id = $this->argument('state_type_id');

        if(!$state_type_id) {
            $state_type_id = '01';
        }

        if ($establishment_id) {
            $documents = Document::query()
                ->where('establishment_id', $establishment_id)
                ->where('state_type_id', $state_type_id)
                //->whereNotIn('response_code', ['1', '2', '3', '4'])
                ->orderBy('series')
                ->orderBy('number')
                ->get();
        } else {
            $documents = Document::query()
                ->where('state_type_id', $state_type_id)
//                ->whereNull('response_code')
//                ->whereNotIn('response_code', ['1', '2', '3', '4'])
                ->orderBy('series')
                ->orderBy('number')
                ->get();
        }

        $count = 0;
        $this->info('-------------------------------------------------');
        $this->info(Company::query()->first()->name);
        $this->info('Documentos:' . count($documents));
        foreach ($documents as $document)
        {
            $count++;
            reValidate:
            $validate_cpe = new ValidateCpe2();
            $response = $validate_cpe->search($document->company->number,
                $document->document_type_id,
                $document->series,
                $document->number,
                $document->date_of_issue,
                $document->total);
            if ($response['success']) {
                $state_type_id = null;
                $response_code = $response['data']['comprobante_estado_codigo'];
                $response_description = $response['data']['comprobante_estado_descripcion'];

                $message = $count.': '.$document->number_full.'|Código: '.$response_code.'|Mensaje: '.$response_description;

                $this->info($message);
                if($response_code !== '1')
                {
                    Log::info($message);
                }

//                if ($response_code === '0') {
//                    $state_type_id = '01';
//                }
//                if ($response_code === '1') {
//                    $state_type_id = '05';
//                }
//                if ($response_code === '2') {
//                    $state_type_id = '11';
//                }
//                if (in_array($response_code, ['0', '1', '2'])) {
//                    $document->update([
//                        'state_type_id' => $state_type_id,
//                        'response_code' => $response_code,
//                        'response_description' => $response_description,
//                    ]);
//                }
            } else {
                goto reValidate;
            }
        }
        $this->info('-------------------------------------------------');

        return ;
    }
}
