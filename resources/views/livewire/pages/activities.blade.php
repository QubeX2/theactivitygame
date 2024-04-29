<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\Member;

new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    public $tags = [];

    public function mount()
    {
    }

    public function updatedSearch()
    {
        $this->tags = strlen($this->search) > 0 ? Activity::where('name', 'like', '%' . $this->search . '%')->get()->toArray() : [];
    }

    public function saveTag($points)
    {
        if(!Activity::where('name', $this->search)->exists()) {
            Activity::create([
                'groupid' => auth()->user()->member->group->id,
                'name' => mb_strtoupper($this->search),
                'points' => $points,
                'touched' => 0,
            ]);
            $this->search = '';
        }
    }
}; ?>

<div class="mt-2">
    <form x-data x-init="$refs.search.focus()" class="flex flex-col w-full gap-y-2">
        <div class="flex w-full justify-center">
            <input x-ref="search" maxlength="14" class="w-80 rounded-lg border-b-2 border-white text-2xl font-bold"
                   type="search" wire:model.live.debounce.150ms="search" placeholder="Search tags...">
        </div>
        @if(sizeof($tags) === 0 && strlen($this->search) > 0)
            <div class="flex flex-col gap-1 justify-center items-center w-full p-2 rounded-lg">
                <div class="text-white font-bold text-2xl">{{__('Tag is missing create it?')}}</div>
                <div class="border-4 border-white bg-red-500 h-10 px-2 pt-1 text-nowrap rounded-full flex gap-x-1 text-2xl items-center justify-center content-center">
                    {{-- <livewire:icon name="star" color="yellow" /> --}}
                    <span class="font-bold">{{mb_strtoupper($this->search)}}</span>
                </div>
                <div class="text-white font-bold text-2xl">{{__('Click one of the points to save')}}</div>
                <div class="flex gap-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <button wire:key="point-{{$i}}" wire:click="saveTag({{$i}})" type="button" class="px-3 font-mono font-bold border-4 border-white rounded-full text-2xl bg-yellow-500">{{$i}}</button>
                    @endfor
                </div>
            </div>
        @endif
        <ul class="flex flex-col gap-y-1">
            @foreach ($tags as $tag)
                <li wire:key="tag-{{$tag['id']}}" class="flex justify-center items-center rounded-lg py-1">
                    <button type="button" class="border-4 border-white bg-red-500 px-2 pt-1 text-nowrap rounded-full flex gap-x-1 text-2xl items-center justify-center content-center">
                        <span class="flex">
                            @for($i = 1; $i <= $tag['points']; $i++)
                                <livewire:icon wire:key="tag-point-{{$tag['id']}}-{{$i}}" name="star" class="mb-0.5" color="yellow" />
                            @endfor
                        </span>
                        <span class="font-bold">{{$tag['name']}}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </form>
</div>
