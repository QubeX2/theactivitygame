<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Goal;
use App\Models\Group;
use App\Models\Member;

new #[Layout('layouts.app')] class extends Component {
    public $points = 2;
    public $typeid = Goal::TYPE_DAILY;

    public function mount()
    {
        $goal = auth()->user()->group->goal;
        if(!auth()->user()->group) {
            $group = Group::create([
                'ownerid' => auth()->user()->id,
            ]);
            Member::create([
                'userid' => auth()->user()->id,
                'groupid' => $group->id,
            ]);
            if(!$goal) {
                $goal = Goal::create([
                    'groupid' => $group->id,
                    'points' => 2,
                    'typeid' => Goal::TYPE_DAILY,
                ]);
            }
        }
        $this->points = $goal->points;
        $this->typeid = $goal->typeid;

    }

    public function saveGoal()
    {
        auth()->user()->group->goal->update([
            'points' => $this->points,
            'typeid' => $this->typeid,
        ]);
        return redirect()->route('activities');
    }
}; ?>

<div class="p-2 bg-yellow-300 min-h-screen">
    @if(!auth()->user()->goal)
        <h1 class="font-xl font-bold">{{__('Please set your goal to continue')}}</h1>
        <div class="flex gap-x-1">
            <input type="number" wire:model="points" class="w-20 text-right rounded-lg" placeholder="Points">
            <select wire:model="typeid" class="rounded-lg">
                <option value="{{\App\Models\Goal::TYPE_DAILY}}">{{__('per day')}}</option>
                <option value="{{\App\Models\Goal::TYPE_WEEKLY}}">{{__('per week')}}</option>
                <option value="{{\App\Models\Goal::TYPE_MONTHLY}}">{{__('per month')}}</option>
            </select>
            <button type="button" wire:click="saveGoal" class="button button-green">Save</button>
        </div>
    @endif
</div>
