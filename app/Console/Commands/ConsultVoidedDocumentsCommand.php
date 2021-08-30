<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Voided;
use Illuminate\Support\Facades\DB;
use App\CoreFacturalo\Facturalo;
use Illuminate\Support\Facades\Log;


class ConsultVoidedDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consult:voided-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consult all documents voided';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $this->info('The command was started');
        $records = Voided::where('state_type_id', '03')->get();

        $fact = DB::connection('tenant')->transaction(function () use($records) {

            foreach ($records as $document) {

                $facturalo = new Facturalo();
                $facturalo->setDocument($document);
                $facturalo->setType('voided');
                $facturalo->statusSummary($document->ticket);
            }
        });

        $this->info("Consulta de documentos anulados, realizado con éxito");

    }
}
