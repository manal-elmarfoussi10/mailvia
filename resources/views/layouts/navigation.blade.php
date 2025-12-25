<nav x-data="{ open: false }" class="bg-black border-b border-white/10">
    <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        {{-- Left: mobile sidebar toggle + brand --}}
        <div class="flex items-center gap-3">
            {{-- Mobile menu button --}}
            <button @click="open = !open"
                class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl
                       text-white/70 hover:text-white hover:bg-white/10 transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <div class="leading-tight">
                    <div class="font-semibold tracking-wide">MAILVIA</div>
                    <div class="text-xs text-white/50">Mailing Platform</div>
                </div>
               
            </a>
        </div>

        {{-- Right: user dropdown --}}
        <div class="hidden sm:flex sm:items-center">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium
                                   text-white/80 hover:text-white hover:bg-white/10 transition">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <!-- Profile link removed - can be added later if needed -->

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    {{-- Mobile drawer (simple) --}}
    <div x-show="open" x-transition class="lg:hidden border-t border-white/10 bg-black">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block rounded-xl px-3 py-2 text-sm text-white/80 hover:bg-white/10 transition">
                Dashboard
            </a>
            <a href="#" class="block rounded-xl px-3 py-2 text-sm text-white/70 hover:bg-white/10 transition">Companies</a>
            <a href="#" class="block rounded-xl px-3 py-2 text-sm text-white/70 hover:bg-white/10 transition">Campaigns</a>
            <a href="#" class="block rounded-xl px-3 py-2 text-sm text-white/70 hover:bg-white/10 transition">Contacts</a>

            <div class="mt-3 pt-3 border-t border-white/10">
                <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-white/60">{{ Auth::user()->email }}</div>

                <div class="mt-3 space-y-1">
                    <!-- Profile link removed -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-xl px-3 py-2 text-sm text-white/70 hover:bg-white/10 transition">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>