<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BitrixPartOne implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $http = new Client(['verify' => false]);
        $link = 'http://nash-crm.kz/api/anticollect/step1.php';
        try{
            $response = $http->get($link,[
               'query' => [
                   'fio' => $data->fio,
                   'phone' => $data->phone,
                   'iin' => $data->iin,
                   'email' => $data->email,
                   'password' => $data->password,
               ],
            ]);
            $response = $response->getBody()->getContents();
        }catch (BadResponseException $e){
            info($e);
        }
    }
}
