{{--
Komponent: Language Switcher
Component: Language Switcher
Opis: Przełącznik języka między PL i EN
Description: Language switcher between PL and EN
--}}
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            class="flex items-center space-x-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
        </svg>
        <span class="text-sm font-medium uppercase">{{ app()->getLocale() }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
         style="display: none;">

        <a href="{{ route('locale.switch', 'en') }}"
           class="block px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300' }} hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex items-center">
                <span class="mr-2">🇬🇧</span>
                <span>English</span>
            </div>
        </a>

        <a href="{{ route('locale.switch', 'pl') }}"
           class="block px-4 py-2 text-sm {{ app()->getLocale() === 'pl' ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300' }} hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="flex items-center">
                <span class="mr-2">🇵🇱</span>
                <span>Polski</span>
            </div>
        </a>
    </div>
</div>
