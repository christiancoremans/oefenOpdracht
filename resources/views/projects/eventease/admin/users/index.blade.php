{{-- resources/views/projects/eventease/admin/users/index.blade.php --}}
{{--
EXAM STUDY NOTE — Admin: manage EventEase user roles
======================================================
Admin-only page. Lists all users with their current ee_role.
A small form on each row lets the admin change the role inline.

Why validate the role on the server side even though we use a <select>?
  → A malicious user could bypass the <select> via a crafted POST request.
  → Server-side 'in:admin,organizer,visitor' validation is the real guard.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">EventEase — User Roles</h1>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-zinc-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">EE Role</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">
                                {{ $user->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    {{ $user->ee_role === 'admin'     ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'         : '' }}
                                    {{ $user->ee_role === 'organizer' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                    {{ $user->ee_role === 'visitor'   ? 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-400'          : '' }}
                                ">
                                    {{ ucfirst($user->ee_role ?? 'visitor') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('eventease.admin.users.update', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="ee_role"
                                            class="border border-gray-300 dark:border-zinc-600 rounded-lg px-2 py-1 text-xs bg-white dark:bg-zinc-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                        <option value="visitor"   {{ $user->ee_role === 'visitor'   ? 'selected' : '' }}>visitor</option>
                                        <option value="organizer" {{ $user->ee_role === 'organizer' ? 'selected' : '' }}>organizer</option>
                                        <option value="admin"     {{ $user->ee_role === 'admin'     ? 'selected' : '' }}>admin</option>
                                    </select>
                                    <button type="submit"
                                            class="bg-emerald-600 text-white px-3 py-1 rounded-lg text-xs hover:bg-emerald-700 transition">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts::project-shell>
