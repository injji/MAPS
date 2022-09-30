<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Models\Client\Service;

class ServiceEndUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '매일 12시 , 사용중이면서 서비스 만료날짜가 지난경우 만료상태로 업데이트';

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
        $today = date('Y-m-d').' 00:00:00';
        $service = Service::where('process',2)
        ->where('service_end_at', '<' , $today)
        ->update(['process' => 3]);
    }
}
