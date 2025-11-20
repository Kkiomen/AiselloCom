<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.descriptions') }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtry -->
        <div class="card">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Szukaj</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('admin.search') }}..."
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">{{ __('admin.all_statuses') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Użytkownik</label>
                    <select name="user_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                        <option value="">{{ __('admin.all_users') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Data od</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Data do</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ __('admin.filter') }}
                </button>

                @if(request()->hasAny(['search', 'status', 'user_id', 'start_date', 'end_date']))
                    <a href="{{ route('admin.descriptions') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Wyczyść
                    </a>
                @endif
            </form>
        </div>

        <!-- Lista -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-2">ID</th>
                            <th class="text-left py-3 px-2">{{ __('admin.user') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.product') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.status') }}</th>
                            <th class="text-right py-3 px-2">OpenAI</th>
                            <th class="text-right py-3 px-2">Serper</th>
                            <th class="text-right py-3 px-2">Zapłacił</th>
                            <th class="text-right py-3 px-2">Zysk</th>
                            <th class="text-left py-3 px-2">{{ __('admin.date') }}</th>
                            <th class="text-left py-3 px-2">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($descriptions as $description)
                            @php
                                $openaiCost = $description->cost ?? 0;
                                $serperCost = $description->apiUsageLog->serper_cost ?? 0;
                                $totalCost = $openaiCost + $serperCost;
                                $revenue = $totalCost * 3;
                                $profit = $totalCost * 2;
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-3 px-2">{{ $description->id }}</td>
                                <td class="py-3 px-2">{{ $description->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-2">{{ Str::limit($description->input_data['name'] ?? 'N/A', 30) }}</td>
                                <td class="py-3 px-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($description->status->value === 'completed') bg-green-100 text-green-800
                                        @elseif($description->status->value === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $description->status->value }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-right text-red-600">${{ number_format($openaiCost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-orange-600">${{ number_format($serperCost, 4) }}</td>
                                <td class="py-3 px-2 text-right text-blue-600">${{ number_format($revenue, 4) }}</td>
                                <td class="py-3 px-2 text-right text-green-600 font-medium">${{ number_format($profit, 4) }}</td>
                                <td class="py-3 px-2">{{ $description->created_at->format('d.m.Y H:i') }}</td>
                                <td class="py-3 px-2">
                                    <a href="{{ route('admin.descriptions.show', $description) }}" class="text-indigo-600 hover:underline">
                                        {{ __('admin.view_details') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-8 text-center text-gray-500">{{ __('admin.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $descriptions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
