{{-- resources/views/projects/devtalk/admin/users/index.blade.php --}}
{{--
EXAM STUDY NOTE — Admin user management
=========================================
Each row has two actions:
  1. Inline PATCH form — select devtalk_role + submit
     PATCH /admin/users/{user}/role → Admin\UserController@update
  2. DELETE form — remove the account
     DELETE /admin/users/{user} → Admin\UserController@destroy

Self-protection: UserController aborts 403 if $user->id === auth()->id().
We also hide the actions visually for the current admin (UX only — the
backend handles it too).

onsubmit="return confirm()" — native browser confirm prevents accidental
deletions without needing JavaScript framework.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">👥 User Management</h1>
            <a href="{{ route('devtalk.admin.dashboard') }}"
               class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← Admin Dashboard</a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 dark:text-gray-400">Name</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 dark:text-gray-400">Email</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 dark:text-gray-400">Forum Role</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 dark:text-gray-400">Change Role</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-400 ml-1">(you)</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs capitalize px-2 py-0.5 rounded-full
                                             {{ $user->devtalk_role === 'admin' ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' :
                                                ($user->devtalk_role === 'moderator' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300' :
                                                'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                    {{ $user->devtalk_role ?? 'user' }}
                                </span>
                            </td>

                            @if($user->id !== auth()->id())
                                {{-- Role change form --}}
                                <td class="px-5 py-3">
                                    <form action="{{ route('devtalk.admin.users.update', $user) }}" method="POST"
                                          class="flex gap-2 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="devtalk_role"
                                                class="border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1 text-xs bg-white dark:bg-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-violet-400">
                                            <option value="user"      {{ $user->devtalk_role === 'user'      ? 'selected' : '' }}>User</option>
                                            <option value="moderator" {{ $user->devtalk_role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                            <option value="admin"     {{ $user->devtalk_role === 'admin'     ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit"
                                                class="bg-violet-600 text-white px-2 py-1 rounded text-xs hover:bg-violet-700 transition">
                                            Save
                                        </button>
                                    </form>
                                </td>
                                {{-- Delete --}}
                                <td class="px-5 py-3">
                                    <form action="{{ route('devtalk.admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 dark:text-red-400 hover:underline text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            @else
                                <td class="px-5 py-3 text-gray-400 text-xs">—</td>
                                <td class="px-5 py-3 text-gray-400 text-xs">—</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-layouts::project-shell>
