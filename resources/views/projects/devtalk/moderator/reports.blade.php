{{-- resources/views/projects/devtalk/moderator/reports.blade.php --}}
{{--
EXAM STUDY NOTE — Moderator reports queue
==========================================
Shows all UNRESOLVED reports (resolved_at IS NULL).
Each row shows:
  - The flagged post excerpt + the thread it belongs to
  - The user who wrote the flagged post
  - The reporter and reason
  - PATCH resolve button → Moderator\ReportController@update

PATCH form: HTML forms don't support PATCH. Laravel uses @method('PATCH')
(a hidden _method field) to override the POST method in routing.

Resolving: sets resolved_at = now() and clears is_flagged on the post
(if no other open reports remain on that post).
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
            🚩 Open Reports <span class="text-base font-normal text-gray-500 dark:text-gray-400">({{ $reports->total() }} total)</span>
        </h1>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($reports->isEmpty())
            <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                ✅ No open reports. The community is doing great!
            </div>
        @else
            <div class="space-y-4">
                @foreach ($reports as $report)
                    <div class="bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700 rounded-xl p-5 shadow-sm">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 min-w-0">
                                {{-- Flagged post info --}}
                                <div class="text-xs text-amber-700 dark:text-amber-300 font-semibold mb-2">
                                    🚩 Flagged post by
                                    <span class="font-bold">{{ $report->post->user->name ?? 'Unknown' }}</span>
                                    in thread:
                                    <a href="{{ route('devtalk.threads.show', $report->post->thread_id ?? 0) }}"
                                       class="hover:underline text-violet-600 dark:text-violet-400">
                                        {{ $report->post->thread->title ?? '—' }}
                                    </a>
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3 text-sm text-gray-700 dark:text-gray-300 mb-3 italic line-clamp-3">
                                    "{{ Str::limit($report->post->body ?? '', 200) }}"
                                </div>

                                {{-- Report meta --}}
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <strong>Reported by:</strong> {{ $report->reporter->name ?? 'Unknown' }}
                                    · {{ $report->created_at->diffForHumans() }}
                                </div>
                                <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                    <strong>Reason:</strong> {{ $report->reason }}
                                </div>
                            </div>

                            {{-- Resolve button --}}
                            <div class="shrink-0 flex items-start sm:items-center">
                                <form action="{{ route('devtalk.moderator.reports.update', $report) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition whitespace-nowrap">
                                        ✅ Mark Resolved
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</x-layouts::project-shell>
