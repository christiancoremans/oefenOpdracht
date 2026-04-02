{{-- resources/views/projects/techbazaar/admin/users/index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('techbazaar.admin.dashboard') }}"
               class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">← Admin Dashboard</a>
            <span class="text-zinc-300">/</span>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Manage Users</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700
                        text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                    shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Role</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-zinc-400">(you)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-500">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleBadge = match($user->role) {
                                        'admin'  => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                        'seller' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                        default  => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleBadge }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->id !== auth()->id())
                                        {{--
                                            EXAM STUDY NOTE — Inline role-change form
                                            Each row has its own PATCH form so the admin
                                            can change roles without a separate edit page.
                                            The select + hidden @method('PATCH') submits
                                            to users.update which handles the role change.
                                        --}}
                                        <form method="POST"
                                              action="{{ route('techbazaar.admin.users.update', $user) }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role"
                                                    class="border border-zinc-300 dark:border-zinc-600 rounded
                                                           px-2 py-1 text-sm bg-white dark:bg-zinc-700
                                                           text-zinc-900 dark:text-white">
                                                <option value="buyer"  @selected($user->role === 'buyer')>Buyer</option>
                                                <option value="seller" @selected($user->role === 'seller')>Seller</option>
                                                <option value="admin"  @selected($user->role === 'admin')>Admin</option>
                                            </select>
                                            <button class="bg-amber-500 text-white px-3 py-1 rounded text-sm
                                                           hover:bg-amber-600">
                                                Save
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('techbazaar.admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Delete {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="border border-red-300 text-red-500 px-3 py-1
                                                           rounded text-sm hover:bg-red-50 dark:hover:bg-red-900/20">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts::project-shell>
