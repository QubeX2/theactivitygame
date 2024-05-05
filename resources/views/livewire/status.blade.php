<?php

use Livewire\Volt\Component;
use App\Models\History;

new class extends Component {
    public $points = 0;
    public $left = 0;
    public $group = 0;

    public function mount()
    {
        $this->refreshPoints();
    }

    public function refreshPoints()
    {
        $this->points = auth()->user()->getPoints();
        $this->left = (auth()->user()->goal?->points ?? 0) - $this->points;
        $this->group = (auth()->user()->group?->goals()->sum('points') ?? 0)
            - (auth()->user()->group?->members->reduce(fn($carry, $item) => $carry + $item->getPoints(), 0) ?? 0);
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
        <div class="flex flex-col">
           <span class="flex gap-x-1 text-indigo-900 sm:text-white">
               @if($left > 0)
                   {{$left}}
                   <span class="text-yellow-500">&#9733;</span>
                   {{__('more to your')}}
                   {{-- auth()->user()->goal?->getTypeText() --}}
                   {{__('goal')}}
               @else
                   {{__('Your')}}
                   {{-- auth()->user()->goal?->getTypeText() --}}
                   {{__('goal is reached!')}}
               @endif
           </span>
            <span class="flex gap-x-1 text-indigo-900 sm:text-white">
               @if($group > 0)
                    {{$group}}
                    <span class="text-yellow-500">&#9733;</span>
                    {{__('more to group')}}
                    {{-- auth()->user()->goal?->getTypeText() --}}
                    {{__('goal')}}
                @else
                    {{__('Group')}}
                    {{-- auth()->user()->goal?->getTypeText() --}}
                    {{__('goal is reached!')}}
                @endif
           </span>
        </div>
    </div>
</div>
