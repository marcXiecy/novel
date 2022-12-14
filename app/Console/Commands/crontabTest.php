<?php

namespace App\Console\Commands;

use App\Services\CommandService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log;

class crontabTest extends Command
{

    protected $signature = 'crontabTest:do';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::channel('daily')->info('crontab run at ' . Carbon::now());
    }
}
