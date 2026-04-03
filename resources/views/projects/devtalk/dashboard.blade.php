{{-- resources/views/projects/devtalk/dashboard.blade.php --}}
{{--
EXAM STUDY NOTE — DevTalk dashboard (role-aware hub)
====================================================
Uses @switch on auth()->user()->devtalk_role to show
role-specific widgets and quick links.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-10">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
            Your DevTalk Dashboard
        </h1>

        @switch(auth()->user()->devtalk_role)

            @case('admin')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('devtalk.admin.dashboard') }}"
                       class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">⚙️</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Admin Dashboard</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Stats, active users, popular threads</div>
                    </a>
                    <a href="{{ route('devtalk.admin.users.index') }}"
                       class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">👥</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Manage Users</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Assign roles, remove accounts</div>
                    </a>
                    <a href="{{ route('devtalk.moderator.reports.index') }}"
                       class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">🚩</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Open Reports</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Review flagged posts</div>
                    </a>
                    <a href="{{ route('devtalk.home') }}"
                       class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">💬</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Browse Threads</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Read and participate in discussions</div>
                    </a>
                </div>
            @break

            @case('moderator')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('devtalk.moderator.reports.index') }}"
                       class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">🚩</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Open Reports</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Review and resolve flagged posts</div>
                    </a>
                    <a href="{{ route('devtalk.home') }}"
                       class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">💬</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Browse Threads</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Read and participate in discussions</div>
                    </a>
                    <a href="{{ route('devtalk.threads.create') }}"
                       class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">✏️</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Start a Thread</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Open a new discussion</div>
                    </a>
                </div>
            @break

            @default
                {{-- Regular user --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('devtalk.home') }}"
                       class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">💬</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Browse Threads</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Explore community discussions</div>
                    </a>
                    <a href="{{ route('devtalk.threads.create') }}"
                       class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 hover:shadow-md transition">
                        <div class="text-2xl mb-1">✏️</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100">Start a Thread</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Ask a question or share something</div>
                    </a>
                </div>
        @endswitch

        <p class="text-sm text-gray-400 dark:text-gray-500">
            Logged in as <strong>{{ auth()->user()->name }}</strong>
            · role: <span class="capitalize">{{ auth()->user()->devtalk_role ?? 'user' }}</span>
        </p>
    </div>
</x-layouts::project-shell>
