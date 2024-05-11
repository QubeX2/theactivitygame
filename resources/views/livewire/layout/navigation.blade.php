<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="sticky top-0 w-full z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl py-4 mx-auto bg-gradient-to-b from-red-500 via-red-700 to-violet-800 rounded-b-3xl shadow-sm shadow-gray-600">
        <div class="flex justify-between">
            <div class="grow flex gap-x-1">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('activities') }}" wire:navigate>
                        <x-user-icon width="64px" height="64px" />
                    </a>
                </div>
                <div class="grow flex flex-col">
                    <div class="font-bold text-white">{{auth()->user()->name}}</div>
                    <hr class="w-5/6" />
                    <livewire:status />
                </div>
                {{--
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('activities') }}" wire:navigate>
                        <x-application-logo class="block h-12 w-auto fill-current" />
                    </a>
                </div>
                --}}
            </div>

            <!-- Hamburger -->
            <div class="me-0 flex items-center">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden bg-white border-b border-b-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('activities')" icon="star" :active="request()->routeIs('activities')" wire:navigate>
                {{ __('Activities') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('history')" icon="history" :active="request()->routeIs('history')" wire:navigate>
                {{ __('History') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('members')" icon="group"  :active="request()->routeIs('members')" wire:navigate>
                {{ __('Members') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings')" icon="settings" :active="request()->routeIs('settings')" wire:navigate>
                {{ __('Settings') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
