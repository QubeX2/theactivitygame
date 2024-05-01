<?php

use Livewire\Volt\Component;
use App\Models\History;

new class extends Component {
    public $points = 0;
    public function mount()
    {
        $this->points = auth()->user()->getPoints();
    }
}; ?>

<div class="p-2 flex h-14">
   <div class="text-indigo-900 sm:text-white text-nowrap font-bold text-lg sm:text-2xl">{!! auth()->user()->group->goal->getText($points) !!}</div>
</div>
