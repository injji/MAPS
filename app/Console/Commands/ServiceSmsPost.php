<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Client\Service;
use DateTime;

class ServiceSmsPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tbl_client_service 테이블을에서 남은기간이 30일 10일 1일 남았을 경우 SMS 알림 발송';

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
     * @return int
     */
    public function handle()
    {
        $today = date('Y-m-d H:i:s');
        $list = Service::where('process',2)
        ->where('period_type',0)
        ->where('service_end_at','>',$today)
        ->get();

        foreach($list as $item)
        {
            // $from = new Datetime(date('Y-m-d'));
            // $to = new Datetime($item->service_end_at->format('Y-m-d'));
            // $dayDiff = date_diff($from,$to);
            $dayDiff = ceil((strtotime($item->service_end_at->format('Y-m-d')) - strtotime(date('Y-m-d'))) / 86400);

            if( $dayDiff == 30 || $dayDiff == 10 || $dayDiff == 1 )
            {
                // 메시지발송
                $phone = \App\Models\Users::find($item->client_id)->manager_phone;
                if($phone)
                {
                    $response = Http::asForm()
                    ->withHeaders([
                        'Authorization' => config('services.phone_api.authorization')
                    ])
                    ->post(config('services.phone_api.url'), [
                        'phone' => str_replace("-", "", $phone),
                        'msg' => '[MAPSTREND] 고객님께서 이용중인 서비스가 곧 만료됩니다. '.PHP_EOL.config('app.domain.client'),
                    ]);

                    // if($response->json()['code'] != 200)
                    // {
                    //     return response()->json(['code' => 402, 'error' => __('messages.send_fail')]);
                    // }

                    // return response()->json([
                    //     'code' => 200,
                    //     'message' => __('messages.save')
                    // ]);
                }
            }
        }

    }
}
