<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\Member;
use App\Models\History;

new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    public $tags = [];

    public function mount()
    {
        if(!auth()->user()->group->goal) {
            return redirect()->route('settings');
        }
        $this->refreshTags();
    }

    public function updatedSearch()
    {
        $this->refreshTags();
    }

    public function refreshTags()
    {
        $this->search = trim($this->search);
        $query = Activity::with([
            'history' => fn($q) => $q->whereRaw('created_at >= now() - interval 1 day')->where('userid', auth()->id())
        ]);
        if(strlen($this->search) > 0) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        $this->tags = $query->orderBy('touched', 'desc')->limit(10)->get()->toArray();
    }

    public function addActivity($id)
    {
        $activity = Activity::find($id);
        $activity->increment('touched');
        History::create([
            'userid' => auth()->user()->id,
            'activityid' => $activity->id,
            'points' => $activity->points,
        ]);
        $this->searh = '';
        $this->refreshTags();
    }

    public function saveTag($points)
    {
        if(!Activity::where('name', $this->search)->exists()) {
            Activity::create([
                'groupid' => auth()->user()->group->id,
                'name' => mb_strtoupper($this->search),
                'points' => $points,
                'touched' => 0,
            ]);
            $this->search = '';
        }
    }
}; ?>

<div class="bg-indigo-500 min-h-screen p-2">
    <form x-data x-init="$refs.search.focus()" class="mt-2 flex flex-col w-full gap-y-2">
        <div class="hidden sm:flex w-full justify-center"><livewire:status /></div>
        @if(auth()->user()->goalFullfilled())
            <div class="flex flex-col w-full justify-center">
                <div class="text-white font-bold text-2xl text-center">{{__('Well done!!')}}</div>
                <div class="text-white font-bold text-2xl text-center">{{__('Nothing to do right now.')}}</div>
                <div class="text-white font-bold text-2xl text-center">{{__('Goal is fullfilled.')}}</div>
            </div>
        @else
            <div class="flex w-full justify-center">
                <input x-ref="search" maxlength="14" class="w-80 rounded-lg border-b-2 border-white text-2xl font-bold"
                       type="search" wire:model.live.debounce.150ms="search" placeholder="{{__('Search tags...')}}">
            </div>
            @if(sizeof($tags) === 0 && strlen($this->search) > 0)
                <div class="flex flex-col gap-1 justify-center items-center w-full p-2 rounded-lg">
                    <div class="text-white font-bold text-2xl">{{__('Tag is missing create it?')}}</div>
                    <div class="border-4 border-white bg-red-500 h-10 px-2 pt-1 text-nowrap rounded-full flex gap-x-1 text-2xl items-center justify-center content-center">
                        <span class="font-bold">{{mb_strtoupper($this->search)}}</span>
                    </div>
                    <div class="text-white font-bold text-2xl">{{__('Click one of the points to save')}}</div>
                    <div class="flex gap-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button wire:key="point-{{$i}}" wire:click="saveTag({{$i}})" type="button" class="cursor-pointer">
                                <svg viewBox="0 0 24 24" width="70" fill="yellow" stroke="black" stroke-width="1" xmlns="http://www.w3.org/2000/svg">
                                    <g stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g>
                                        <path d="M9.15316 5.40838C10.4198 3.13613 11.0531 2 12 2C12.9469 2 13.5802 3.13612 14.8468 5.40837L15.1745 5.99623C15.5345 6.64193 15.7144 6.96479 15.9951 7.17781C16.2757 7.39083 16.6251 7.4699 17.3241 7.62805L17.9605 7.77203C20.4201 8.32856 21.65 8.60682 21.9426 9.54773C22.2352 10.4886 21.3968 11.4691 19.7199 13.4299L19.2861 13.9372C18.8096 14.4944 18.5713 14.773 18.4641 15.1177C18.357 15.4624 18.393 15.8341 18.465 16.5776L18.5306 17.2544C18.7841 19.8706 18.9109 21.1787 18.1449 21.7602C17.3788 22.3417 16.2273 21.8115 13.9243 20.7512L13.3285 20.4768C12.6741 20.1755 12.3469 20.0248 12 20.0248C11.6531 20.0248 11.3259 20.1755 10.6715 20.4768L10.0757 20.7512C7.77268 21.8115 6.62118 22.3417 5.85515 21.7602C5.08912 21.1787 5.21588 19.8706 5.4694 17.2544L5.53498 16.5776C5.60703 15.8341 5.64305 15.4624 5.53586 15.1177C5.42868 14.773 5.19043 14.4944 4.71392 13.9372L4.2801 13.4299C2.60325 11.4691 1.76482 10.4886 2.05742 9.54773C2.35002 8.60682 3.57986 8.32856 6.03954 7.77203L6.67589 7.62805C7.37485 7.4699 7.72433 7.39083 8.00494 7.17781C8.28555 6.96479 8.46553 6.64194 8.82547 5.99623L9.15316 5.40838Z"></path>
                                        <text x="8" y="16" fill="black" stroke-width="0" font-family="Verdana" font-weight="800" font-size="10">{{$i}}</text>
                                    </g>
                                </svg>
                            </button>
                        @endfor
                    </div>
                </div>
            @endif
            <ul class="flex flex-col gap-y-1">
                @foreach ($tags as $tag)
                    @if(!sizeof($tag['history']))
                        <li wire:key="tag-{{$tag['id']}}" class="flex justify-center items-center rounded-lg py-1">
                            <x-tag :points="$tag['points']" :id="$tag['id']" :name="$tag['name']" />
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </form>
</div>
