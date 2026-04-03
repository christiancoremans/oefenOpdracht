{{-- resources/views/projects/devtalk/admin/dashboard.blade.php --}}
{{--
EXAM STUDY NOTE — Admin dashboard (statistics overview)
=========================================================
4 stat cards: total users, threads, posts, open reports.
2 tables:
  1. Most active users — sorted by forum_posts_count (withCount)
  2. Popular threads   — sorted by posts_count (withCount)

withCount() used in DashboardController adds {relation}_count dynamically:
  User::withCount('forumPosts') → $user->forum_posts_count
  Thread::withCount('posts')   → $thread->posts_count
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-8">⚙️ DevTalk Admin Dashboard</h1>

        {{-- ── Stat cards ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700 rounded-xl p-5 text-center">
                <div class="text-3xl font-bold text-violet-700 dark:text-violet-300">{{ number_format($totalUsers) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Users</div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 text-center">
                <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($totalThreads) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Threads</div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-5 text-center">
                <div class="text-3xl font-bold text-green-700 dark:text-green-300">{{ number_format($totalPosts) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Replies</div>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-5 text-center">
                <div class="text-3xl font-bold text-amber-700 dark:text-amber-300">{{ number_format($openReports) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Open Reports</div>
                @if($openReports > 0)
                    <a href="{{ route('devtalk.moderator.reports.index') }}"
                       class="text-xs text-amber-600 dark:text-amber-400 hover:underline mt-1 inline-block">
                        View →
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ── Most active users ── --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Most Active Users</h2>
                    <a href="{{ route('devtalk.admin.users.index') }}"
                       class="text-xs text-violet-600 dark:text-violet-400 hover:underline">Manage users →</a>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">#</th>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">User</th>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">Role</th>
                            <th class="text-right px-5 py-2 font-medium text-gray-600 dark:text-gray-400">Replies</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($mostActiveUsers as $i => $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs capitalize px-2 py-0.5 rounded-full
                                                 {{ $user->devtalk_role === 'admin' ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' :
                                                    ($user->devtalk_role === 'moderator' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300' :
                                                    'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                        {{ $user->devtalk_role ?? 'user' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format($user->forum_posts_count) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ── Popular threads ── --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Most Active Threads</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">#</th>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">Thread</th>
                            <th class="text-left px-5 py-2 font-medium text-gray-600 dark:text-gray-400">Category</th>
                            <th class="text-right px-5 py-2 font-medium text-gray-600 dark:text-gray-400">Replies</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($popularThreads as $i => $thread)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('devtalk.threads.show', $thread) }}"
                                       class="font-medium text-gray-800 dark:text-gray-200 hover:text-violet-600 dark:hover:text-violet-400 line-clamp-1">
                                        {{ $thread->title }}
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $thread->category->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format($thread->posts_count) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts::project-shell>
