<x-guest-layout>
    <!-- Nagłówek formularza / Form header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ __('auth.register_title') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            {{ __('auth.register_subtitle') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Imię i nazwisko / Name -->
        <div>
            <x-input-label for="name" :value="__('auth.name')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input
                id="name"
                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('auth.name_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Nazwa firmy (opcjonalnie) / Company name (optional) -->
        <div>
            <x-input-label for="company_name" class="text-gray-700 dark:text-gray-300 font-medium">
                {{ __('auth.company_name') }}
                <span class="text-gray-400 font-normal">({{ __('auth.optional') }})</span>
            </x-input-label>
            <x-text-input
                id="company_name"
                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="text"
                name="company_name"
                :value="old('company_name')"
                autocomplete="organization"
                placeholder="{{ __('auth.company_name_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Adres email / Email Address -->
        <div>
            <x-input-label for="email" :value="__('auth.email')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input
                id="email"
                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                placeholder="{{ __('auth.email_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Hasło / Password -->
        <div>
            <x-input-label for="password" :value="__('auth.password')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input
                id="password"
                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('auth.password_new_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('auth.password_requirements') }}</p>
        </div>

        <!-- Potwierdź hasło / Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('auth.password_confirmation')" class="text-gray-700 dark:text-gray-300 font-medium" />
            <x-text-input
                id="password_confirmation"
                class="block mt-2 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="{{ __('auth.password_confirm_placeholder') }}"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Zgoda na regulamin / Terms agreement -->
        <div class="flex items-start">
            <input
                id="terms"
                type="checkbox"
                class="h-4 w-4 mt-0.5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800"
                name="terms"
                required
            >
            <label for="terms" class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                {!! __('auth.terms_agreement', [
                    'terms' => '<a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">' . __('auth.terms_of_service') . '</a>',
                    'privacy' => '<a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">' . __('auth.privacy_policy') . '</a>'
                ]) !!}
            </label>
        </div>

        <!-- Przycisk rejestracji / Register button -->
        <div>
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                {{ __('auth.register_button') }}
            </button>
        </div>

        <!-- Link do logowania / Login link -->
        <div class="text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('auth.already_registered') }}
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                    {{ __('auth.login_link') }}
                </a>
            </p>
        </div>
    </form>

    <!-- Korzyści z rejestracji / Registration benefits -->
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('auth.register_benefits_title') }}</h3>
        <ul class="space-y-2">
            <li class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('auth.benefit_api_keys') }}
            </li>
            <li class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('auth.benefit_playground') }}
            </li>
            <li class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('auth.benefit_usage') }}
            </li>
        </ul>
    </div>
</x-guest-layout>
