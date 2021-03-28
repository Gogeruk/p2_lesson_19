<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visit;
use App\Models\Ad;
use Illuminate\Http\Request;
use Hillel\Iplookupinterface\IpLookupInterface;
use Hillel\Useragentlookupinterface\UserAgentInterface;
use App\Jobs\ProcessVisits;

class ShowAds extends Component
{
    use WithPagination;

    public function render(Request $request)
    {
        $request = [
            $request->ip(),
            $request->header('User-Agent')
        ];

        ProcessVisits::dispatch($request)
            ->onQueue('parsing');

        return view('livewire.show-ads',
            ['ads' => Ad::paginate(5)]
        );
    }

    public function like()
    {
        $this->ad->addLikeBy(auth()->user());
    }
}
