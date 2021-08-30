<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Storage;

class ChangelogGen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'changelog:update {--from= : tag inicial} {--to= : tag reciente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Autocompletado del archivo changelog';

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
        $option_from = $this->option('from');
        $option_to = $this->option('to');
        if ($option_to == '' || $option_from == '') {
            $this->error('debe ingresar tags del repositorio');
            return false;
        }

        $keys = ['feature', 'fixed', 'docs'];

        $path = base_path();

        foreach ($keys as $key) {
            $gitlog = new Process('git log '.$option_from.'..'.$option_to.' --no-merges --date=short --pretty=format:"%ad : %s" | grep -e '.$key);
            $gitlog->run();
            if (!$gitlog->isSuccessful()) {
                $this->error('no hay datos para '.$key);
            } else {
                $res_gitlog = $gitlog->getOutput();
                $br = str_replace("\n", "<br>\n", $res_gitlog);

                $new_content = file_get_contents($path.'/CHANGELOG.md');
                file_put_contents($path.'/CHANGELOG.md', $br."\n"."\n".$new_content);

                $content = file_get_contents($path.'/CHANGELOG.md');
                file_put_contents($path.'/CHANGELOG.md', "### ".$key."\n".$content);
            }

        }

        $update_content = file_get_contents($path.'/CHANGELOG.md');
        file_put_contents($path.'/CHANGELOG.md', '## '.$option_to."\n"."\n".$update_content);
        $this->info('ha finalizado la actualización');
    }
}
