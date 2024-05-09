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
        $this->users = auth()->user()->group?->members()->select('users.id', 'users.name')
            ->with(['goal'])->orderBy('name')->get()->toArray() ?? [];
    }
}; ?>

<div class="p-2 bg-white min-h-screen flex flex-col">
    <h1 class="text-2xl font-bold">{{__('History')}}</h1>
    <ul class="flex flex-col">
        @foreach($users as $user)
            @php $ou = \App\Models\User::find($user['id']); @endphp
            <li class="flex flex-col pb-3">
                <h2 class="text-xl font-bold">
                    {{$user['name']}}
                    ({{$ou->getPoints()}} of {{$user['goal']['points']}}
                    <span class="text-yellow-500 text-lg">&#9733;</span>
                    {{\App\Models\Goal::typeText($user['goal']['typeid'])}})
                </h2>
                <hr class="border border-gray-300">
                <span class="">
                    <ul>
                        @foreach(\App\Models\User::find($user['id'])->history()->goalType($user['goal']['typeid'])->get() as $entry)
                            <li class="grid grid-cols-5 gap-x-1 font-bold items-center justify-center">
                                <span class="font-xl col-span-2">{{( new DateTime($entry['created_at']))->format('Y-m-d H:m')}}</span>
                                <span class="font-xl col-span-2">{{$entry['activity']['name']}}</span>
                                <span class="font-xl flex flex-nowrap">
                                @foreach(range(1, $entry['points']) as $point)
                                    <span class="text-yellow-500 text-lg">&#9733;</span>
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                </span>
            </li>
        @endforeach
    </ul>
</div>
