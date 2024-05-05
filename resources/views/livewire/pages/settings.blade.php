<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Goal;
use App\Models\Group;
use App\Models\Member;
use App\Models\Activity;
use Livewire\Attributes\Url;

new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    public $points = 2;
    public $typeid = Goal::TYPE_DAILY;
    public $tags = [];

    #[Url(as: 'first-time')]
    public $first;

    public function mount()
    {
        $goal = auth()->user()->goal;
        $group = auth()->user()->group;
        if(!$group) {
            $group = Group::create([
                'ownerid' => auth()->user()->id,
            ]);
            Member::create([
                'userid' => auth()->user()->id,
                'groupid' => $group->id,
            ]);
        }
        if(!$goal) {
            $this->goals = false;
            $goal = Goal::create([
                'userid' => auth()->user()->id,
                'groupid' => $group->id,
                'points' => 2,
                'typeid' => Goal::TYPE_DAILY,
            ]);
        }
        $this->points = $goal->points;
        $this->typeid = $goal->typeid;
    }

    public function updatedSearch()
    {
        $this->refreshTags();
    }

    public function refreshTags()
    {
        $search = trim($this->search);
        $query = Activity::query();
        if(strlen($search) > 0) {
            $query->where('name', 'like', "%{$search}%");
        }
        $this->tags = $query->orderBy('touched', 'desc')->limit(10)->get()->toArray();
    }

    public function saveTag($id, $index)
    {
        $tag = Activity::find($id);
        $tag->update([
            'name' => $this->tags[$index]['name'],
            'points' => $this->tags[$index]['points'],
        ]);
        $this->refreshTags();
    }

    public function deleteTag($id)
    {
        Activity::where('id', $id)->delete();
        $this->refreshTags();
    }

    public function saveGoal()
    {
        auth()->user()->goal->update([
            'points' => $this->points,
            'typeid' => $this->typeid,
        ]);
        if($this->first == 1) {
            return redirect()->route('activities');
        }
    }
}; ?>

<div class="p-2 bg-yellow-300 min-h-screen flex flex-col items-center gap-y-8">
    <div>
        <h1 class="font-xl font-bold">{{__('Please set your goal to continue')}}</h1>
        <div class="flex gap-x-1">
            <input type="number" wire:model="points" class="w-20 text-right rounded-lg" placeholder="Points">
            <select wire:model="typeid" class="rounded-lg">
                <option value="{{\App\Models\Goal::TYPE_DAILY}}">{{__('per day')}}</option>
                <option value="{{\App\Models\Goal::TYPE_WEEKLY}}">{{__('per week')}}</option>
                <option value="{{\App\Models\Goal::TYPE_MONTHLY}}">{{__('per month')}}</option>
            </select>
            <button type="button" wire:click="saveGoal" class="button button-green"><livewire:icon name="save" size="36" color="darkgreen" /></button>
        </div>
    </div>
    <div>
        <h1 class="font-xl font-bold">{{__('Edit tags')}}</h1>
        <div x-data x-init="$refs.search.focus()" class="flex w-full justify-center">
            <input x-ref="search" maxlength="14" class="w-80 rounded-lg border-b-2 border-white text-2xl font-bold"
                   type="search" wire:model.live.debounce.150ms="search" placeholder="{{__('Search tag')}}">
        </div>
    </div>
    <ul x-data class="flex flex-col gap-y-1">
        @foreach ($tags as $index => $tag)
            <li wire:key="tag-{{$tag['id']}}" class="flex gap-x-1 justify-center items-center rounded-lg py-1">
                <x-text-input wire:model="tags.{{$index}}.name" class="font-bold" />
                <select wire:model="tags.{{$index}}.points">
                    @for($i = 1; $i <= 3; $i++)
                        <option wire:key="point-{{$i}}" value="{{$i}}" @if($tag['points'] == $i) selected @endif>{{$i}}</option>
                    @endfor
                </select>
                <button wire:click="saveTag({{$tag['id']}}, {{$index}})" class="button button-green"><livewire:icon wire:key="save-{{$tag['id']}}" name="save" size="36" color="darkgreen" /></button>
                <button type="button" wire:click="deleteTag({{$tag['id']}})" class="button button-red"><livewire:icon wire:key="delete-{{$tag['id']}}" name="delete" size="36" color="darkred" /></button>
            </li>
        @endforeach
    </ul>
</div>
