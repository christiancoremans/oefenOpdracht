<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\Music\Instructor;
use App\Models\Music\Reservation;
use App\Models\Music\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — WorkshopController (MusicHub)
|--------------------------------------------------------------------------
| Full CRUD — but index/show are also public (no auth required).
| create/store/edit/update/destroy are protected by ['auth','music_role:admin']
| middleware in the route file — no need to repeat the check here.
|
| CONFLICT DETECTION (bonus feature):
|   Room conflict:       same room + overlapping times
|   Instructor conflict: same instructor + overlapping times
|
|   Overlap condition between A and B:
|     A.start < B.end  AND  A.end > B.start
|   We exclude the current workshop itself on updates (->where('id','!=',$id)).
|
| IMAGE UPLOAD:
|   - store() uses $request->hasFile('picture') before storing.
|   - update() deletes old image from disk before saving new one.
|   - destroy() deletes image from disk before deleting DB row.
|   - Use Storage::disk('public')->delete($path) to clean up.
|   - In views: Storage::url($workshop->picture) for the public URL.
|   - Run: php artisan storage:link (once) to make public/storage accessible.
|
| ->sync($instructorIds) vs ->attach():
|   sync()  → replaces ALL current instructor links with the new selection.
|             Perfect for edit/update: selects new set, removes old ones.
|   attach() → adds without removing existing. Used in store() when there's
|              nothing to replace yet (or sync works in store too — same result).
|--------------------------------------------------------------------------
*/

class WorkshopController extends Controller
{
    private function projectData(): array
    {
        return [
            'currentProject'     => 'music',
            'projectName'        => config('projects.music.name'),
            'projectDescription' => config('projects.music.description'),
        ];
    }

    // ── Public: Workshop overview list ────────────────────────────────────────
    public function index()
    {
        $workshops = Workshop::withCount('reservations')
            ->with('instructors')
            ->orderBy('start_time')
            ->get();

        return view('projects.music.home', array_merge($this->projectData(), [
            'workshops' => $workshops,
        ]));
    }

    // ── Public: Workshop detail (the "main" page) ─────────────────────────────
    public function show(Workshop $workshop)
    {
        $workshop->load('instructors', 'reservations');

        $userReservation = null;
        $workshopFull    = $workshop->reservations->count() >= $workshop->capacity;

        if (auth()->check()) {
            $userReservation = $workshop->reservations
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('projects.music.workshops.show', array_merge($this->projectData(), [
            'workshop'         => $workshop,
            'userReservation'  => $userReservation,
            'workshopFull'     => $workshopFull,
        ]));
    }

    // ── Admin: Show create form ───────────────────────────────────────────────
    public function create()
    {
        return view('projects.music.workshops.create', array_merge($this->projectData(), [
            'instructors' => Instructor::orderBy('name')->get(),
        ]));
    }

    // ── Admin: Store new workshop ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'start_time'     => 'required|date|after:now',
            'end_time'       => 'required|date|after:start_time',
            'room'           => 'required|string|max:100',
            'capacity'       => 'required|integer|min:1',
            'picture'        => 'nullable|image|max:2048',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:music_instructors,id',
        ]);

        // ── Conflict checks ───────────────────────────────────────────────────
        $this->checkRoomConflict($request->room, $request->start_time, $request->end_time);
        $this->checkInstructorConflict($request->instructor_ids ?? [], $request->start_time, $request->end_time);

        // ── Image upload ──────────────────────────────────────────────────────
        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('workshops', 'public');
        }

        $workshop = Workshop::create($validated);
        $workshop->instructors()->sync($request->input('instructor_ids', []));

        return redirect()->route('music.workshops.show', $workshop)
            ->with('success', 'Workshop created successfully!');
    }

    // ── Admin: Show edit form ─────────────────────────────────────────────────
    public function edit(Workshop $workshop)
    {
        return view('projects.music.workshops.edit', array_merge($this->projectData(), [
            'workshop'    => $workshop,
            'instructors' => Instructor::orderBy('name')->get(),
        ]));
    }

    // ── Admin: Update existing workshop ──────────────────────────────────────
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'start_time'     => 'required|date',
            'end_time'       => 'required|date|after:start_time',
            'room'           => 'required|string|max:100',
            'capacity'       => 'required|integer|min:1',
            'picture'        => 'nullable|image|max:2048',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:music_instructors,id',
        ]);

        // ── Conflict checks (exclude self) ────────────────────────────────────
        $this->checkRoomConflict($request->room, $request->start_time, $request->end_time, $workshop->id);
        $this->checkInstructorConflict($request->instructor_ids ?? [], $request->start_time, $request->end_time, $workshop->id);

        // ── Image upload ──────────────────────────────────────────────────────
        if ($request->hasFile('picture')) {
            if ($workshop->picture) {
                Storage::disk('public')->delete($workshop->picture);
            }
            $validated['picture'] = $request->file('picture')->store('workshops', 'public');
        }

        $workshop->update($validated);
        $workshop->instructors()->sync($request->input('instructor_ids', []));

        return redirect()->route('music.workshops.show', $workshop)
            ->with('success', 'Workshop updated successfully!');
    }

    // ── Admin: Delete workshop ────────────────────────────────────────────────
    public function destroy(Workshop $workshop)
    {
        if ($workshop->picture) {
            Storage::disk('public')->delete($workshop->picture);
        }

        $workshop->delete();

        return redirect()->route('music.home')
            ->with('success', "\"{$workshop->title}\" has been deleted.");
    }

    // ── Private helpers: conflict detection ───────────────────────────────────

    private function checkRoomConflict(string $room, string $start, string $end, ?int $excludeId = null): void
    {
        $query = Workshop::where('room', $room)
            ->where(function ($q) use ($start, $end) {
                // Overlap: existing.start < new.end AND existing.end > new.start
                $q->where('start_time', '<', $end)
                  ->where('end_time',   '>', $start);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            abort(422, "Room \"{$room}\" is already booked during this time slot.");
        }
    }

    private function checkInstructorConflict(array $instructorIds, string $start, string $end, ?int $excludeId = null): void
    {
        if (empty($instructorIds)) {
            return;
        }

        $query = Workshop::whereHas('instructors', function ($q) use ($instructorIds) {
                $q->whereIn('music_instructors.id', $instructorIds);
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time',   '>', $start);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            abort(422, 'One or more selected instructors are already scheduled during this time slot.');
        }
    }
}
