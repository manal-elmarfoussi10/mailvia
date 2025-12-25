<x-guest-layout>
    <div class="min-h-screen bg-black text-white flex items-center justify-center px-4 py-10 relative overflow-hidden">

        {{-- background glow --}}
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full blur-3xl opacity-30"
             style="background: radial-gradient(circle, rgba(124,58,237,.9), rgba(124,58,237,0));"></div>

        <div class="absolute -bottom-40 -right-40 h-[28rem] w-[28rem] rounded-full blur-3xl opacity-25"
             style="background: radial-gradient(circle, rgba(34,211,238,.8), rgba(34,211,238,0));"></div>

        <div class="absolute inset-0 opacity-[0.15]"
             style="background-image:
                radial-gradient(circle at 20% 10%, rgba(255,255,255,.08), transparent 40%),
                radial-gradient(circle at 80% 90%, rgba(255,255,255,.06), transparent 45%);">
        </div>

        <div class="relative w-full max-w-md">
            {{-- Brand --}}
            <div class="mb-8 text-center">
                <img src="{{ asset('logo.png') }}" alt="Mailvia" class="mx-auto h-14 w-auto">
                <p class="mt-3 text-sm text-white/70">
                    Send via SMTP or API. Track. Control. Deliver.
                </p>
            </div>

            {{-- Card --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur">
                <div class="mb-6">
                    <h1 class="text-xl font-semibold tracking-tight">Welcome back</h1>
                    <p class="mt-1 text-sm text-white/60">Sign in to your Mailvia workspace.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-sm text-green-200" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-white/80" />
                        <x-text-input
                            id="email"
                            class="mt-2 block w-full rounded-xl border-white/10 bg-black/40 text-white placeholder:text-white/30
                                   focus:border-violet-400/50 focus:ring-0"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@mailvia.cloud"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="text-white/80" />
                        <x-text-input
                            id="password"
                            class="mt-2 block w-full rounded-xl border-white/10 bg-black/40 text-white placeholder:text-white/30
                                   focus:border-cyan-300/50 focus:ring-0"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-white/70">
                            <input
                                id="remember_me"
                                type="checkbox"
                                class="h-4 w-4 rounded border-white/20 bg-black/40 text-violet-500
                                       focus:ring-0 focus:outline-none"
                                name="remember"
                            >
                            <span>{{ __('Remember me') }}</span>
                        </label>

                     
                    </div>

                    <div class="pt-2">
                        {{-- custom button (instead of x-primary-button) --}}
                        <button
                            type="submit"
                            class="w-full rounded-xl px-4 py-3 font-semibold text-white
                                   bg-gradient-to-r from-violet-600 to-cyan-500
                                   hover:from-violet-500 hover:to-cyan-400
                                   shadow-lg shadow-violet-900/20
                                   focus:outline-none focus:ring-4 focus:ring-violet-500/25
                                   transition"
                        >
                            {{ __('Log in') }}
                        </button>

                        <p class="mt-4 text-center text-xs text-white/40">
                            © {{ date('Y') }} Mailvia — Internal mailing platform
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>