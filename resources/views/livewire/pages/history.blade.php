<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use \App\Models\History;

new #[Layout('layouts.app')] class extends Component {
    public $history = [];

    public function mount()
    {
        $this->history = auth()->user()->group?->history()
            ->with(['activity', 'user:id,name'])
            ->orderBy('created_at', 'desc')->get()
            ->groupBy(fn($x) => $x['user']['name'])->toArray() ?? [];
    }
}; ?>

<div class="p-2 bg-red-300 min-h-screen flex flex-col">
    <h1 class="text-2xl font-bold border-b-2 border-b-red-950">{{__('History')}}</h1>
    <ul class="flex flex-col">
        @foreach($history as $user => $entries)
            <li class="flex flex-col">
                <h2 class="text-xl font-bold border-b border-b-red-950">{{$user}}</h2>
                <span class="">
                    <ul>
                        @foreach($entries as $entry)
                            <li class="grid grid-cols-5 gap-x-1">
                                <span class="font-xl col-span-2">{{( new DateTime($entry['created_at']))->format('Y-m-d H:m')}}</span>
                                <span class="font-xl col-span-2">{{$entry['activity']['name']}}</span>
                                <span class="font-xl flex flex-nowrap">
                                @foreach(range(1, $entry['points']) as $point)
                                    <span class="text-yellow-200 text-lg">&#9733;</span>
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                </span>
            </li>
        @endforeach
    </ul>
</div>
