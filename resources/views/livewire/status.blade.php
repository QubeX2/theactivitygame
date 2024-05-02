<?php

use Livewire\Volt\Component;
use App\Models\History;

new class extends Component {
    public $points = 0;
    public $left = 0;

    public function mount()
    {
        $this->refreshPoints();
    }

    public function refreshPoints()
    {
        $this->points = auth()->user()->getPoints();
        $this->left = (auth()->user()->group?->goal?->points ?? 0) - $this->points;
    }

    public function getListeners()
    {
        return [
            'refresh-points' => 'refreshPoints',
        ];
    }

}; ?>

<div class="px-2 flex h-14 items-center">
    <div class="font-bold text-lg sm:text-4xl">
       <span class="flex gap-x-1 text-indigo-900 sm:text-white">
           @if($left > 0)
               {{$left}}
               <span class="text-yellow-500">&#9733;</span>
               {{__('more to the')}}
               {{auth()->user()->group->goal->getTypeText()}}
               {{__('goal')}}
           @else
               {{__('Your')}}
               {{auth()->user()->group->goal->getTypeText()}}
               {{__('goal is reached!')}}
           @endif
       </span>
    </div>
</div>
