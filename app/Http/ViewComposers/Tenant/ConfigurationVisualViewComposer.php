<?php

namespace App\Http\ViewComposers\Tenant;

use App\Http\Resources\Tenant\ConfigurationResource;
use App\Models\Tenant\Configuration;

class ConfigurationVisualViewComposer
{
    public function compose($view)
    {
        $configuration = Configuration::first();
        if(is_null($configuration->visual)) {
            $defaults = [
                'bg' => 'light',
                'header' => 'light',
                'sidebars' => 'light',
            ];
            $configuration->visual = $defaults;
            $configuration->save();
        }
        $configuration = Configuration::first();
        $record = new ConfigurationResource($configuration);
        $view->visual = $record->visual;
    }
}
