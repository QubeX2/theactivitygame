<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\Member;
use App\Models\History;

/**
 * TODO: Show history and a diagram when done
 * TODO: Fix data for chart
 */
new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    public $activities = [];
    public $info;

    public function mount()
    {
        $this->info = auth()->user()->getInfo();
        if(!$this->info->goal || !$this->info->groupid) {
            return redirect()->route('settings', ['first-time' => true]);
        }
        $this->refreshTags();
    }

    public function updatedSearch(): void
    {
        $this->refreshTags();
    }

    public function refreshTags(): void
    {
        $this->info = auth()->user()->getInfo();
        $search = trim($this->search);
        $query = Activity::whereDoesntHave('history', fn($q) => $q->today()->userItems($this->info->userid));
        if(strlen($search) > 0) {
            $query->where('name', 'like', "%{$search}%");
        }
        if($this->info->left === $this->info->mandatory) {
            $query->where('mandatory', true);
        }
        $this->activities = $query->orderBy('mandatory', 'desc')->orderBy('touched', 'desc')->limit(10)->get()->toArray();
        $this->dispatch('refresh-chart', data: $this->info->weekly);
    }

    public function addActivity($id): void
    {
        $activity = Activity::find($id);
        $activity->increment('touched');
        History::create([
            'userid' => $this->info->userid,
            'groupid' => $this->info->groupid,
            'activityid' => $activity->id,
            'points' => $activity->points,
        ]);
        $this->searh = '';
        $this->dispatch('refresh-points');
        $this->refreshTags();
    }

    public function saveTag($points): void
    {
        if(!Activity::where('name', $this->search)->exists()) {
            Activity::create([
                'groupid' => $this->info->groupid,
                'name' => mb_strtoupper($this->search),
                'points' => $points,
                'touched' => 0,
            ]);
            $this->search = '';
        }
    }
}; ?>

@assets
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@endassets
@script
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        let options = {
            title: @js(__('Weekly activities')),
            legend: { position: 'bottom' },
            colors: ['rgb(185, 28, 28)'],
            pointsVisible: true,
            vAxis: {
                viewWindow: {
                    min: 0,
                    max: @js($this->info->goal),
                }
            }
        };

        let chart = new google.visualization.LineChart(document.getElementById('id-chart'));
        Livewire.on('refresh-chart', ( { data: data }) => {
            refreshChart(chart, options, data);
        });
    }

    function refreshChart(chart, options, data) {
        console.log(Object.entries(data).map(e =>e.map(x => +x)))
        let dt = google.visualization.arrayToDataTable([
            [@js(__('Day')), @js(__('Activity points'))],
            [@js(__('Mon')), @js($this->info->weekly[0] ?? 0)],
            [@js(__('Tue')), @js($this->info->weekly[1] ?? 0)],
            [@js(__('Wed')), @js($this->info->weekly[2] ?? 0)],
            [@js(__('Thu')), @js($this->info->weekly[3] ?? 0)],
            [@js(__('Fri')), @js($this->info->weekly[4] ?? 0)],
            [@js(__('Sat')), @js($this->info->weekly[5] ?? 0)],
            [@js(__('Sun')), @js($this->info->weekly[6] ?? 0)],
        ]);
        chart.draw(dt, options);
    }
</script>
@endscript
<div class="min-h-screen p-2 bg-white">
    <form class="mt-2 flex flex-col sm:mx-auto gap-y-2 items-center">
        @if($this->info->left === 0)
            <div class="flex flex-col w-full justify-center">
                <div class="text-white font-bold text-2xl text-center">{{__('Well done!!')}}</div>
                <div class="text-white font-bold text-2xl text-center">{{__('Nothing to do right now.')}}</div>
            </div>
        @else
            @if(strlen($this->search) >= 2 && collect($activities)->filter(fn($x) => $x['name'] === mb_strtoupper($this->search))->isEmpty())
                <div class="flex flex-col gap-1 justify-center items-center w-full p-2 rounded-lg">
                    <div class="border-4 border-white bg-red-500 h-10 px-2 pt-1 text-nowrap rounded-full flex gap-x-1 text-2xl items-center justify-center content-center">
                        <span class="font-bold">{{mb_strtoupper($this->search)}}</span>
                    </div>
                    <div class="text-white font-bold text-2xl text-center">{{__('Is missing, click a point to add!')}}</div>
                    <div class="flex gap-x-1">
                        @for($i = 1; $i <= 3; $i++)
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
            <div x-data x-init="$refs.search.focus()" class="flex w-full justify-center">
                <input x-ref="search" maxlength="14" class="w-80 rounded-lg border-b-2 border-gray-400 text-md font-bold"
                       type="search" wire:model.live.debounce.150ms="search" placeholder="{{__('Search activity')}}">
            </div>
            <ul class="flex flex-col gap-y-1 px-4 w-80">
                @foreach ($activities as $activity)
                    <li wire:key="activity-{{$activity['id']}}" class="rounded-lg py-1">
                        <x-activity :mandatory="$activity['mandatory']" :points="$activity['points']" :id="$activity['id']" :name="$activity['name']" />
                    </li>
                @endforeach
            </ul>
            <div wire:ignore id="id-chart" class="w-full"></div>
        @endif

    </form>
</div>
