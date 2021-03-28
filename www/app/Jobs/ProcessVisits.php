<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Hillel\Iplookupinterface\IpLookupInterface;
use Hillel\Useragentlookupinterface\UserAgentInterface;
use App\Models\Visit;
use Throwable;

class ProcessVisits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( IpLookupInterface  $reader,
                            UserAgentInterface $result)
    {
        $new_request = new Request;
        $new_request->headers->set('User-Agent' ,$this->request[1]);

        $reader->ipParse($this->request[0]);
        $result->userAgentParse($new_request);

        Visit::create([
            'ip'             => $this->request[0],
            'iso_code'       => $reader->isoCode(),
            'continent_code' => $reader->continentCode(),
            'browser'        => $result->userAgentBrowser(),
            'os'             => $result->userAgentOs(),
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Visit::create([
            'ip'             => 'anonymous',
            'iso_code'       => 'anonymous',
            'continent_code' => 'anonymous',
            'browser'        => 'anonymous',
            'os'             => 'anonymous',
        ]);
    }
}
