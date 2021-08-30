<?php

namespace App\Console\Commands;

use Facades\App\Http\Controllers\Tenant\DocumentController;
use Illuminate\Console\Command;
use App\Traits\CommandTrait;
use App\Traits\OfflineTrait;
use App\Models\Tenant\{
    Configuration,
    Document
};

class SendAllServerCommand extends Command
{
    use CommandTrait, OfflineTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline:send-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all pending documents to the online server';

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
            if (!$this->isOffline()) {
                $this->info('The offline service is disabled');

                return;
            };

            $documents = Document::query()
                ->where('send_server', 0)
                ->where('success_shipping_status', false)
                // ->orWhere('shipping_status', '!=', '')
                ->get();

            foreach ($documents as $document) {
                try {
                    $response = DocumentController::sendServer($document->id);

                    // // $document->shipping_status = '';

                    if(isset($response['success'])){

                        $document->success_shipping_status = $response['success'];
                        $document->shipping_status = ($response['success'])? json_encode(array_merge($response,['message' => 'El envío al servidor online fué exitoso'])): json_encode(array_merge($response,['message' => 'El envío al servidor online falló']));

                    }else{

                        $document->success_shipping_status = false;
                        $document->shipping_status = json_encode($response);

                    }

                    $document->save();
                }
                catch (\Exception $e) {

                    $document->success_shipping_status = false;
                    $document->shipping_status = json_encode([
                                                    'success' => false,
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
