<?php

namespace App\Console\Commands;

use App\Services\SnowService;
use Illuminate\Console\Command;

class snow extends Command
{

    protected $signature = 'snow:generate';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $time1 = time();
        $snows = [];
        $snow = new SnowService(0, 0);
        for ($i = 0; $i < 100000; $i++) {
            $snows[] = $snow->nextId();
            $this->info("\nSnowId: ".$snow->nextId());
        }
        $time2 = time();
        $this->info($time1);
        $this->info($time2);
        $this->info(count(array_unique($snows)));
    }
}
