<?php

namespace App\Console\Commands;

use GuzzleHttp\Client as ClientGuzzleHttp;
use Illuminate\Console\Command;
use Hyn\Tenancy\Models\Website;
use App\Models\Tenant\{
    Configuration,
    Summary,
    Company,
    User
};
use Auth;

class SummaryQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summary:query';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic query of summaries';

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
        $this->info('The command was started');

        Auth::login(User::firstOrFail());

        if (Configuration::firstOrFail()->cron) {
            $hostname = Website::query()
                ->where('uuid', app(\Hyn\Tenancy\Environment::class)->tenant()->uuid)
                ->first()
                ->hostnames
                ->first();

            $summaries = Summary::query()
                ->where([
                    'soap_type_id' => Company::firstOrFail()->active()->soap_type_id,
                    'summary_status_type_id' => '1',
                    'state_type_id' => '03',
                ])
                ->get();

            foreach ($summaries as $summary) {

                // if(file_exists(base_path(config('tenant.name_certificate_cron')))){
                //     $constructor_params = [
                //         'base_uri' => config('tenant.force_https') ? "https://{$hostname->fqdn}" : "http://{$hostname->fqdn}",
                //         'verify' => base_path(config('tenant.name_certificate_cron'))
                //     ];
                // }else{
                //     $constructor_params = [
                //         'base_uri' => config('tenant.force_https') ? "https://{$hostname->fqdn}" : "http://{$hostname->fqdn}"
                //     ];
                // }

                $constructor_params = [
                    'base_uri' => config('tenant.force_https') ? "https://{$hostname->fqdn}" : "http://{$hostname->fqdn}",
                    'verify' => false
                ];

                $clientGuzzleHttp = new ClientGuzzleHttp($constructor_params);

                $response = $clientGuzzleHttp->post('/api/summaries/status', [
                    'http_errors' => false,
                    'headers' => [
                        'Authorization' => 'Bearer '.auth()->user()->api_token,
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'external_id' => $summary->external_id,
                        'ticket' => $summary->ticket
                    ]
                ]);

                $res = json_decode($response->getBody()->getContents(), true);

                if (!$res['success']) $this->info("{$summary->external_id} - {$summary->ticket} - {$res['message']}");
            }
        }
        else {
            $this->info('The crontab is disabled');
        }

        $this->info('The command is finished');
    }
}
