<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use \App\Models\History;

/**
 * TODO: Change history to view month
 * TODO: Fix layout
 */
new #[Layout('layouts.app')] class extends Component {
    public $history = [];
    public $users = [];

    public function mount()
    {
        $this->history = auth()->user()->group?->history()->with(['user:id,name'])->thisMonth()->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function deleteHistory($id)
    {
        History::find($id)->delete();
    }
}; ?>

<div class="px-4 py-2 bg-white min-h-screen flex flex-col rounded-3xl shadow shadow-gray-600">
    <h1 class="text-2xl font-bold">{{__('History')}}</h1>
    <ul class="flex flex-col">
        @foreach($this->history as $entry)
            <li class="flex gap-x-1 items-center justify-stretch">
                <span class="grow">{{$entry['user']['name']}}</span>
                <span class="font-xl col-span-2">{{\Illuminate\Support\Carbon::parse($entry['created_at'])->diffForHumans()}}</span>
                <span class="font-xl col-span-2 font-bold w-64">
                    {{$entry['name']}}
                    @foreach(range(1, $entry['points']) as $point)
                        <span class="text-yellow-500 text-lg">&#9733;</span>
                    @endforeach
                </span>
                <button type="button"
                    @if(auth()->id() === auth()->user()->group?->ownerid || auth()->id() === $entry['user']['id']) wire:click="deleteHistory({{$entry['id']}})" @endif
                    wire:confirm="{{__('Are you sure you want to delete this history?')}}">
                    <i class="material-icons text-red-500">delete</i>
                </button>
            </li>
        @endforeach
    </ul>
</div>
