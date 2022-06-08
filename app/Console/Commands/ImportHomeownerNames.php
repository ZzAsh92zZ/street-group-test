<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\HomeownerNames;

class ImportHomeownerNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:homeowner-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports homeowner names from a CSV file';

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
     * @return void
     */
    public function handle()
    {
        $this->line('');
        $this->info('Importing Data...');
        (new HomeownerNames())->withOutput($this->output)->import(storage_path('app/examples__284_29.csv'));
        $this->info('Complete');
        $this->line('');
    }
}
