<?php

namespace Luezoid\Laravelcore\Console\Command;

use Illuminate\Console\Command;

class FilesInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'luezoid:files:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates all the files directory paths listed in config/file for local paths.';

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
        $fileConfig = config('file');
        foreach ($fileConfig['types'] ?? [] as $key => $config) {
            if (isset($config['local_path']) && !file_exists(public_path() . '/' . $config['local_path'])) {
                mkdir(public_path() . '/' . $config['local_path'], 0775, true);
            }
        }
    }
}
