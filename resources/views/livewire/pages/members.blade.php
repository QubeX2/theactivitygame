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

<div class="p-2 bg-white min-h-screen sm:px-10 rounded-3xl shadow shadow-gray-600">
    <form class="flex flex-col gap-y-4 w-full">
        @if(auth()->id() == auth()->user()->group->ownerid)
            <h1 class="text-2xl text-black font-bold">{{__('Invite someone')}}</h1>
            <div class="flex gap-x-1">
                <input type="email" wire:model="email" placeholder="{{__('Email')}}" class="rounded-lg w-80" />
                <button wire:click="inviteMember()" class="button text-violet-600">
                    <i class="">person_add</i>
                </button>
            </div>
        @endif
        @if(sizeof($invitations))
            <h1 class="text-2xl text-black font-bold">{{__('Active invitations')}}</h1>
            <div class="flex flex-col">
                @foreach($invitations as $invitation)
                    <div class="flex gap-x-1">
                        <span class="basis-3/4 font-xl">{{$invitation['email']}}</span>
                        <button wire:confirm="{{__('Are you sure you want to cancel this invitation')}}?" wire:click="cancelInvitation({{$invitation['id']}})" class="button text-red-500">
                            <i>cancel</i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        <h1 class="text-2xl text-black font-bold">{{__('Members')}}</h1>
        <div class="flex flex-col gap-y-1">
            @if(sizeof($members) > 0)
                @foreach($members as $member)
                    <div class="flex gap-x-1 p-1">
                        <span class="basis-3/4 text-xl">{{$member['name']}}</span>
                        @if(auth()->user()->id == auth()->user()->group->ownerid)
                            <button wire:confirm="{{__('Are you sure you want to remove this member')}}?" wire:click="removeUser({{$member['id']}})" class="button text-red-500">
                                <i>delete</i>
                            </button>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </form>
</div>
