<x-guest-layout>
    <!-- Nagłówek formularza / Form header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ __('auth.login_title') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">
            {{ __('auth.login_subtitle') }}
        </p>
    </div>

    <!-- Status sesji / Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Adres email / Email Address -->
        <div>
            <x-input-label for="email" :value="__('auth.email')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
            <x-text-input
                id="email"
                class="block mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="{{ __('auth.email_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Hasło / Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('auth.password')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300" href="{{ route('password.request') }}">
                        {{ __('auth.forgot_password') }}
                    </a>
                @endif
            </div>
            <x-text-input
                id="password"
                class="block mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="{{ __('auth.password_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Zapamiętaj mnie / Remember Me -->
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800"
                name="remember"
            >
            <label for="remember_me" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <!-- Przycisk logowania / Login button -->
        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            {{ __('auth.login_button') }}
        </button>

        <!-- Link do rejestracji / Registration link -->
        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('auth.no_account') }}
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                {{ __('auth.register_link') }}
            </a>
        </p>
    </form>
</x-guest-layout>
