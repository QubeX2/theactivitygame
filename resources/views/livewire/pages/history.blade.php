<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use \App\Models\History;

new #[Layout('layouts.app')] class extends Component {
    public $history = [];
    public $users = [];

    public function mount()
    {
        $this->users = auth()->user()->group?->members()->select('users.id', 'users.name')
            ->with(['goal'])->orderBy('name')->get()->toArray() ?? [];
        /*
        $this->history = auth()->user()->group?->history()
            ->with(['activity', 'user:id,name'])
            ->whereRaw('month(created_at) = month(curdate())')
            ->whereRaw('year(created_at) = year(curdate())')
            ->orderBy('created_at', 'desc')->get()
            ->groupBy(fn($x) => $x['user']['id'])->toArray() ?? [];
        */
    }
}; ?>

<div class="p-2 bg-white min-h-screen flex flex-col">
    <h1 class="text-2xl font-bold">{{__('History')}}</h1>
    <ul class="flex flex-col">
        @foreach($users as $user)
            @php $ou = \App\Models\User::find($user['id']); @endphp
            <li class="flex flex-col pb-3">
                <h2 class="text-xl font-bold bg-red-900 text-white px-1">
                    {{$user['name']}}
                    ({{$ou->getPoints()}} of {{$user['goal']['points']}}
                    <span class="text-yellow-200 text-lg">&#9733;</span>
                    {{\App\Models\Goal::typeText($user['goal']['typeid'])}})
                </h2>
                <span class="">
                    <ul>
                        @foreach(\App\Models\User::find($user['id'])->history()->goalType($user['goal']['typeid'])->get() as $entry)
                            <li class="grid grid-cols-5 gap-x-1 font-bold items-center justify-center">
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
