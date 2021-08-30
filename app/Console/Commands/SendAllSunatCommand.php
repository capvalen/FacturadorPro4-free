<?php

namespace App\Console\Commands;

use Facades\App\Http\Controllers\Tenant\DocumentController;
use Illuminate\Console\Command;
use App\Traits\CommandTrait;
use App\Models\Tenant\{
    Configuration,
    Document
};

class SendAllSunatCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online:send-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all pending documents to be sent to the Sunat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if (Configuration::firstOrFail()->cron) {
            if ($this->isOffline()) {
                $this->info('Offline service is enabled');

                return;
            }

            $documents = Document::query()
                ->where('group_id', '01')
                ->where('send_server', 0)
                ->whereIn('state_type_id', ['01','03'])
                // ->orWhere('sunat_shipping_status', '!=', '')
                ->where('success_sunat_shipping_status', false)
                ->get();

            foreach ($documents as $document) {
                try {
                    $response = DocumentController::send($document->id);

                    $document->sunat_shipping_status = json_encode($response);
                    $document->success_sunat_shipping_status = true;
                    $document->save();
                }
                catch (\Exception $e) {

                    $document->success_sunat_shipping_status = false;

                    $document->sunat_shipping_status = json_encode([
                        'sucess' => false,
                        'message' => $e->getMessage(),
                        'payload' => $e
                    ]);

                    $document->save();
                }
            }
        }
        else {
            $this->info('The crontab is disabled');
        }

        $this->info('The command is finished');
    }
}
