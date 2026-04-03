{{-- resources/views/projects/drivesmart/admin/users/index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-8 space-y-6">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Manage Users</h1>
            <a href="{{ route('drivesmart.admin.dashboard') }}"
               class="text-sm text-sky-600 dark:text-sky-400 hover:underline">← Back to admin</a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-700/50 text-left">
                        <tr>
                            <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">DS Role</th>
                            <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Change Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $user->name }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $roleColors = [
                                        'admin'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                        'instructor' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                                        'student'    => 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-300',
                                    ];
                                    $roleColor = $roleColors[$user->ds_role ?? 'student'] ?? $roleColors['student'];
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColor }}">
                                    {{ $user->ds_role ?? 'student' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('drivesmart.admin.users.update', $user) }}"
                                      class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="ds_role"
                                            class="border border-gray-300 dark:border-zinc-600 rounded-lg px-2 py-1 text-xs bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                                        @foreach(['student','instructor','admin'] as $role)
                                            <option value="{{ $role }}" {{ ($user->ds_role ?? 'student') === $role ? 'selected' : '' }}>
                                                {{ ucfirst($role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-sky-600 text-white text-xs px-3 py-1 rounded-lg hover:bg-sky-700 transition">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts::project-shell>
