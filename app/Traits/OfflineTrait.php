<?php

namespace App\Traits;
use Modules\Offline\Models\OfflineConfiguration;

trait OfflineTrait
{ 

    private function getIsClient() {
        return (bool) OfflineConfiguration::firstOrFail()->is_client;
    }

    private function getUrlServer() {
        return OfflineConfiguration::firstOrFail()->url_server;
    }
    
    private function getTokenServer() {
        return OfflineConfiguration::firstOrFail()->token_server;
    }
    
}
