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
        $this->invitaions = Invite::where('userid', auth()->user()->id)->get()->toArray();
        $this->members = auth()->user()->group->members->toArray();
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

}; ?>

<div class="p-2 bg-green-500 min-h-screen">
    <form class="flex flex-col gap-1 content-center items-center w-full">
        <h1 class="text-2xl text-black font-bold">{{__('Invite a member to join your activities')}}</h1>
        <div class="flex gap-x-1">
            <input type="email" wire:model="email" placeholder="{{__('Email')}}" class="rounded-lg w-60" />
            <button wire:click="inviteMember()" class="button button-yellow">{{__('Invite')}}</button>
        </div>
        @if(sizeof($invitations))
            <h1 class="text-2xl text-black font-bold">{{__('Invitations')}}</h1>
            @foreach($invitations as $invitation)
                <div class="flex gap-x-1">
                    <p>{{__('Invitation to')}} {{$invitation['email']}}</p>
                    <button wire:click="cancelInvitation({{$invitation['id']}})" class="button button-red">{{__('Cancel')}}</button>
                </div>
            @endforeach
        @endif
        @if(sizeof($members) > 0)
            <h1 class="text-2xl text-black font-bold">{{__('Members')}}</h1>
            @foreach($members as $member)
                <div class="flex gap-x-1">
                    <p>{{$member['name']}} &lt;{{$member['email']}}&gt;</p>
                </div>
            @endforeach
        @endif
    </form>
</div>
