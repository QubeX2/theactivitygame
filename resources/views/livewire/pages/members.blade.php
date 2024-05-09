<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use App\Mail\InviteMember;
use App\Models\Invite;

new #[Layout('layouts.app')] class extends Component {
    public $email;
    public $invitations = [];
    public $members = [];

    public function mount()
    {
        $this->invitations = Invite::where('groupid', auth()->user()->group?->id)->where('accepted', false)->get()->toArray() ?? [];
        $this->members = auth()->user()->group?->members()->get()->toArray() ?? [];
    }

    public function inviteMember()
    {
        $token = Str::uuid();
        Invite::create([
            'email' => $this->email,
            'token' => $token,
            'userid' => auth()->user()->id,
            'groupid' => auth()->user()->group->id
        ]);
        Mail::to($this->email)->send(new InviteMember($token));
    }

    public function cancelInvitaion($id)
    {

    }

    public function removeUser($id)
    {

    }
}; ?>

<div class="p-2 bg-white min-h-screen sm:px-10">
    <form class="flex flex-col gap-y-4 w-full">
        @if(auth()->id() == auth()->user()->group->ownerid)
            <h1 class="text-2xl text-black font-bold">{{__('Invite someone')}}</h1>
            <div class="flex gap-x-1">
                <input type="email" wire:model="email" placeholder="{{__('Email')}}" class="rounded-lg w-60" />
                <button wire:click="inviteMember()" class="button button-yellow pl-1 pr-2 flex items-center">
                    <i class="material-icons w-8 flex items-center justify-center text-xl text-red-950">person_add</i>
                    {{__('Invite')}}
                </button>
            </div>
        @endif
        @if(sizeof($invitations))
            <h1 class="text-2xl text-black font-bold">{{__('Active invitations')}}</h1>
            <div class="flex flex-col">
                @foreach($invitations as $invitation)
                    <div class="flex gap-x-1">
                        <span class="basis-3/4 font-xl">{{$invitation['email']}}</span>
                        <button wire:confirm="Do you want to cancel this invitation?" wire:click="cancelInvitation({{$invitation['id']}})" class="button button-red">
                            <i class="material-icons w-8 flex items-center justify-center text-xl text-red-950">delete</i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        <h1 class="text-2xl text-black font-bold">{{__('Members')}}</h1>
        <div class="flex flex-col">
            @if(sizeof($members) > 0)
                @foreach($members as $member)
                    <div class="flex gap-x-1">
                        <span class="basis-3/4 text-xl">{{$member['name']}}</span>
                        @if(auth()->user()->id == auth()->user()->group->ownerid)
                            <button wire:confirm="Do you want to remove this member?" wire:click="removeUser({{$member['id']}})" class="button button-red">
                                <i class="material-icons w-8 flex items-center justify-center text-xl text-red-950">delete</i>
                            </button>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </form>
</div>
