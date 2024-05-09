<?php

use Livewire\Volt\Component;
use App\Models\History;

/**
* TODO: Fix counter of mandatory
*/
new class extends Component {
    public $points = 0;
    public $left = 0;
    public $goal = 0;

    public function mount()
    {
        $this->refreshPoints();
    }

    public function refreshPoints()
    {
        $this->points = auth()->user()->getPoints();
        $this->left = (auth()->user()->goal?->points ?? 0) - $this->points;
        $this->goal = auth()->user()->goal?->points;
    }

    public function getListeners()
    {
        return [
            'refresh-points' => 'refreshPoints',
        ];
    }

}; ?>

<div class="px-2 flex h-14 items-center">
    <div class="font-bold">
        <div class="flex flex-col">
            <span class="flex gap-x-1 text-sm">
                {{__('Your')}}
                {{auth()->user()->goal?->getTypeText()}}
                {{__('goal is')}}
                {{$this->goal}}
                <span class="text-yellow-500">&#9733;</span>
            </span>
            <span class="flex gap-x-1 text-white">
               @if($left > 0)
                   {{__('You have')}}
                    {{$left}}
                    <span class="text-yellow-500">&#9733;</span>
                    {{__('left to do')}}
                @else
                    {{__('Great, the goal is reached!')}}
                @endif
           </span>
        </div>
    </div>
</div>
