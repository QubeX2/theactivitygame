<?php

use Livewire\Volt\Component;
use App\Models\History;

/**
 * TODO: Fix counter of mandatory
 */
new class extends Component {
    public $info = null;

    public function mount()
    {
        $this->refreshPoints();
    }

    public function refreshPoints(): void
    {
        $this->info = auth()->user()->getInfo();
    }

    public function getListeners(): array
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
                {{$this->info->goalTypeText}}
                {{__('goal is')}}
                {{$this->info->goal}}
                <span class="text-yellow-500">&#9733;</span>
            </span>
            <span class="flex gap-x-1 text-white">
               @if($this->info->left > 0)
                    {{__('You have')}}
                    {{$this->info->left}}
                    <span class="text-yellow-500">&#9733;</span>
                    {{__('left to do')}}
                @else
                    {{__('Great, the goal is reached!')}}
                @endif
           </span>
        </div>
    </div>
</div>
