<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('admin.users') }}</h1>
        </div>
    </x-slot>

    <div class="card">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 px-2">ID</th>
                        <th class="text-left py-3 px-2">{{ __('admin.name') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.email') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.company') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.api_keys') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.descriptions') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.admin') }}</th>
                        <th class="text-left py-3 px-2">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-2">{{ $user->id }}</td>
                            <td class="py-3 px-2">{{ $user->name }}</td>
                            <td class="py-3 px-2">{{ $user->email }}</td>
                            <td class="py-3 px-2">{{ $user->company_name ?? '-' }}</td>
                            <td class="py-3 px-2">{{ $user->api_keys_count }}</td>
                            <td class="py-3 px-2">{{ $user->product_descriptions_count }}</td>
                            <td class="py-3 px-2">
                                @if($user->is_admin)
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Admin</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">User</span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-indigo-600 hover:underline">
                                        {{ $user->is_admin ? __('admin.remove_admin') : __('admin.make_admin') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">{{ __('admin.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
