<?php

namespace App\Http\Controllers\System;

use Config;
use Artisan;
use DateTime;
use Exception;
use App\Traits\BackupTrait;
use Illuminate\Http\Request;
use App\Models\System\Client;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Models\Hostname;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{

    use BackupTrait;

    public function index() {

        $avail = new Process('df -m -h --output=avail /');
        $avail->run();
        $disc_used = $avail->getOutput();

        $df = new Process('du -sh '.storage_path().' | cut -f1');
        $df->run();
        $storage_size = $df->getOutput();

        $most_recent = $this->mostRecent();

        $clients = Client::without(['hostname','plan'])
            ->select('hostname_id', 'name')
            ->get();

        return view('system.backup.index')->with('disc_used', $disc_used)->with('storage_size', $storage_size)->with('last_zip', $most_recent)->with('clients', $clients);
    }

    public function db(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individual,todos',
            'hostname_id' => 'nullable|required_if:type,individual',
        ]);

        $database = '';
        if ($request->type === 'individual') {
            $hostname = Hostname::findOrFail($request->hostname_id);
            $website = Website::findOrFail($hostname->website_id);
            $database = $website->uuid;
        }
        $output = Artisan::call('bk:bd', [
            'type' => $request->type,
            'database' => $database,
        ]);
        return json_encode($output);
    }

    public function files(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individual,todos',
            'hostname_id' => 'nullable|required_if:type,individual',
        ]);

        $folder = '';
        if ($request->type === 'individual') {
            $hostname = Hostname::findOrFail($request->hostname_id);
            $website = Website::findOrFail($hostname->website_id);
            $folder = $website->uuid;
        }
        $output = Artisan::call('bk:files', [
            'type' => $request->type,
            'folder' => $folder,
        ]);
        return json_encode($output);
    }

    public function upload(Request $request)
    {

        $config = [
            'driver' => 'ftp',
            'host'   => $request['host'],
            'port' => $request['port'],
            'username' => $request['username'],
            'password'   => $request['password'],
            'port'  => 21,
            'passive'   => true,
        ];

        Config::set('filesystems.disks.ftp', $config);

        // definimos y subimos el archivo
        try {

            $most_recent = $this->mostRecent();

            $fileTo = $most_recent['name'];
            // $fileFrom = storage_path('app/'.$most_recent['path']);

            $fileFrom = Storage::get($most_recent['path']);

            $upload = Storage::disk('ftp')->put($fileTo, $fileFrom);

            return [
                'success' => $upload,
                'message' => 'Proceso finalizado satisfactoriamente'
            ];


        } catch (Exception $e) {

            $this->setErrorLog($e);
            return $this->getErrorMessage("Lo sentimos, ocurrió un error inesperado: {$e->getMessage()}");

        }

    }

    public function mostRecent()
    {
        $zips = Storage::allFiles('backups/zip/');

        if (count($zips) > 0) {
            $name_zips = [];
            $most_recent_time = '';
            $last_date = null;

            foreach($zips as $zip){
                $zip_explode = explode( '/', $zip);
                if(count($zip_explode) <= 3){
                    array_push($name_zips, $zip_explode[2]);
                    $last = Storage::lastModified($zip);
                    $datetime = new DateTime("@$last");
                    if ($datetime > $most_recent_time) {
                        $most_recent_time = $datetime;
                        $most_recent_path = $zip;
                        $most_recent_name = $zip_explode[2];
                        $last_date = $last;
                    }
                }
            }

            return [
                'date' => \Carbon\Carbon::createFromTimestamp($last_date)->format('d-m-Y \a \l\a\s H:i'),
                'path' => $most_recent_path,
                'name' => $most_recent_name
            ];
        } else {
            return '';
        }
    }


    public function download($filename)
    {

        return Storage::download('backups'.DIRECTORY_SEPARATOR.'zip'.DIRECTORY_SEPARATOR.$filename);

    }

}
