<?php

namespace App\Http\Controllers\DevTalk\Moderator;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Report;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Moderator\ReportController
|--------------------------------------------------------------------------
| Moderators (and admins) can:
|   - View all UNRESOLVED reports (post flags, user complaints)
|   - Mark a report as resolved by setting resolved_at = now()
|
| soft-resolve vs hard-delete: We keep resolved reports for audit history.
| A moderator can look back and see what was reported and when it was
| resolved. resolved_at acts as a soft "close" flag.
|
| scopeUnresolved() is defined on the Report model:
|   whereNull('resolved_at')
|
| Eager loading: we need post content (to show what was flagged) and
| reporter identity (to see who flagged it). Both come via with().
|   with(['post.user', 'reporter']) → nested eager loading in one call:
|   post (the Post model) + post.user (the poster's User) + reporter (User)
|--------------------------------------------------------------------------
*/
class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::unresolved()
            ->with(['post.user', 'post.thread', 'reporter'])
            ->latest()
            ->paginate(20);

        return view('projects.devtalk.moderator.reports', [
            'reports'            => $reports,
            'currentProject'     => 'devtalk',
            'projectName'        => 'DevTalk',
            'projectDescription' => 'Developer discussion forum',
        ]);
    }

    public function update(Report $report)
    {
        $report->update(['resolved_at' => now()]);

        // Also clear the flagged state on the post if no remaining open reports
        if ($report->post && Report::where('post_id', $report->post_id)->unresolved()->doesntExist()) {
            $report->post->update(['is_flagged' => false]);
        }

        return redirect()->back()->with('success', 'Report marked as resolved.');
    }
}
