<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Activity;
use App\Models\Member;
use App\Models\History;

/**
 * TODO: Fix design for chart
 */
new #[Layout('layouts.app')] class extends Component {
    public $search = '';
    public $activities = [];
    public $points = 1;
    public $info;
    public $history = [];

    public function mount()
    {
        $this->info = auth()->user()->getInfo();
        if(!$this->info->goal || !$this->info->groupid) {
            return redirect()->route('settings', ['first-time' => true]);
        }
        $this->refreshActivities();
    }

    public function updatedSearch(): void
    {
        $this->refreshActivities();
    }

    public function refreshActivities(): void
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
        $this->activities = $query->orderBy('mandatory', 'desc')->orderBy('touched', 'desc')->limit(5)->get()->toArray();
    }

    public function refreshChart(): void
    {
        $this->dispatch('refresh-chart', data: array_values($this->info->weekly));
    }

    public function addActivity($id): void
    {
        $activity = Activity::find($id);
        $activity->increment('touched');
        History::create([
            'name' => $activity->name,
            'userid' => $this->info->userid,
            'groupid' => $this->info->groupid,
            'activityid' => $activity->id,
            'points' => $activity->points,
        ]);
        $this->searh = '';
        $this->dispatch('refresh-points');
        $this->refreshActivities();
        $this->refreshChart();
    }

    public function saveActivity(): void
    {
        if(!Activity::where('name', $this->search)->exists()) {
            Activity::create([
                'groupid' => $this->info->groupid,
                'name' => mb_strtoupper($this->search),
                'points' => $this->points,
                'touched' => 0,
            ]);
            $this->search = '';
            $this->points = 1;
        }
    }
}; ?>

@assets
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@endassets
@script
<script type="text/javascript">
    let dayNames = ['{{__('Mon')}}', '{{__('Tue')}}', '{{__('Wed')}}', '{{__('Thu')}}', '{{__('Fri')}}', '{{__('Sat')}}', '{{__('Sun')}}'];
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        let options = {
            title: @js(__('Weekly activities')),
            legend: { position: 'bottom' },
            colors: ['rgb(234, 179, 8)'],
            pointsVisible: true,
            pointShape: 'star',
            pointSize: 30,
            lineWidth: 0,
            backgroundColor: 'transparent',
            vAxis: {
                viewWindow: {
                    min: 0,
                    max: @js($this->info->goal),
                }
            }
        };

        let chart = new google.visualization.LineChart(document.getElementById('id-chart'));
        refreshChart(chart, options, @js(array_values($this->info->weekly)));
        Livewire.on('refresh-chart', ( { data: data }) => {
            refreshChart(chart, options, data);
        });
    }

    function refreshChart(chart, options, data) {
        let d = Object.entries(data).map(([key, value]) => [dayNames[key], value === 0 ? null : value]);

        let dt = google.visualization.arrayToDataTable([
            [@js(__('Day')), @js(__('Activity points'))],
            ...d
        ]);
        chart.draw(dt, options);
    }
</script>
@endscript
<div class="min-h-screen p-2 bg-white rounded-3xl shadow shadow-gray-600">
    <form class="mt-2 flex flex-col sm:mx-auto gap-y-2 items-center">
        @if($this->info->left === 0)
            <div class="flex flex-col w-full justify-center items-center">
                <div>
                    <svg width="64" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 512"><circle fill="#FBD433" transform="matrix(2.64389 -.70843 .70843 2.64389 256 256)" r="93.505"/><path fill="#40270E" d="M118.959 278.462c69.505-2.158 212.527 1.215 274.08-.171 0 165.703-274.08 181.545-274.08.171zm85.834-84.99c-30.555-23.781-59.738-23.427-87.565-.261-12.545 10.444-18.125 4.612-12.458-9.576 5.636-14.108 12.708-26.114 21.661-35.367 37.814-39.081 72.372-4.168 88.914 34.677 5.285 12.402 2.539 20.715-10.552 10.527zm191.566 0c-30.557-23.781-59.74-23.427-87.565-.261-12.544 10.444-18.127 4.612-12.46-9.576 5.636-14.108 12.708-26.114 21.661-35.367 37.814-39.081 72.371-4.168 88.917 34.677 5.282 12.402 2.535 20.715-10.553 10.527z"/><path fill="#fff" d="M118.959 278.462c69.506-6.316 212.527-2.806 274.08-.172.786 58.937-273.259 58.212-274.08.172z"/><path fill="red" d="M163.715 377.761c46.294 40.045 132.671 41.615 184.568 0-28.257-32.14-59.077-33.112-93.282-7.846-35.265-28.197-58.875-28.995-91.286 7.846z"/></svg>
                </div>
                <div class="font-bold text-2xl w-96 text-center">{{__('Awesome!')}}</div>
                <div class="font-bold text-2xl w-96 text-center">{{__('There is nothing to do right now, come back tomorrow.')}}</div>
            </div>
        @else
            @if(strlen($this->search) >= 2 && collect($activities)->filter(fn($x) => $x['name'] === mb_strtoupper($this->search))->isEmpty())
                <div class="flex flex-col gap-1 justify-center items-center w-full p-2 rounded-lg">
                    <div class="w-80">
                        <x-activity :points="1" :name="mb_strtoupper($this->search)" />
                    </div>
                    <div class="font-bold text-2xl text-center">{{__('The activity is missing!')}}</div>
                    <div class="flex flex-col gap-y-2">
                        @for($i = 1; $i <= 3; $i++)
                            <label class="flex items-center w-full">
                                <input type="radio" wire:model="points" value="{{$i}}"/>
                                @for($j = 0; $j < $i; $j++)
                                    <i class="material-icons text-yellow-500">star</i>
                                @endfor
                            </label>
                        @endfor
                        <button wire:click="saveActivity()" class="button button-green">
                            <i>save</i> Save activity
                        </button>
                    </div>
                </div>
            @endif
            <div x-data x-init="$refs.search.focus()" class="flex w-full justify-center">
                <input x-ref="search" maxlength="16" class="w-80 rounded-lg border-b-2 border-gray-400 text-md font-bold"
                       type="search" wire:model.live.debounce.150ms="search" placeholder="{{__('Search activity')}}">
            </div>
            <ul class="flex flex-col gap-y-1 px-4 w-80">
                @foreach ($activities as $activity)
                    <li wire:key="activity-{{$activity['id']}}" class="rounded-lg py-1">
                        <x-activity :mandatory="$activity['mandatory']" :points="$activity['points']" :id="$activity['id']" :name="$activity['name']" />
                    </li>
                @endforeach
            </ul>
        @endif
        <div>{{__('Todays activities')}}</div>
        <ul class="flex flex-col gap-y-1 px-4 w-80">
            @foreach ($this->info->history as $index => $history)
                <li wire:key="history-{{$index}}" class="rounded-lg py-1">
                    <x-activity :points="$history['points']" :name="$history['name']" />
                </li>
            @endforeach
        </ul>
        <div class="w-full sm:w-96">
            <div wire:ignore id="id-chart" class="bg-white rounded-xl shadow shadow-gray-400 w-full"></div>
        </div>

    </form>
</div>
