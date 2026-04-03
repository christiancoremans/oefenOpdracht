<?php

namespace App\Http\Controllers\EventEase;

use App\Http\Controllers\Controller;
use App\Models\EventEase\Event;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — OrganizerEventController
|--------------------------------------------------------------------------
| Handles CRUD for events — only accessible to organizers and admins
| (protected by 'ee_role:organizer,admin' middleware in routes/eventease.php).
|
| Admins can manage ALL events.
| Organizers can only manage THEIR OWN events.
|
| The ownershipCheck() helper enforces this:
|   → If you are an admin: pass through.
|   → If you are an organizer: abort(403) if the event belongs to someone else.
|
| Why a separate controller from EventController?
|   → EventController is public-facing (index, show — no auth needed).
|   → OrganizerEventController is management-facing (create, store, edit,
|     update, destroy — organizer/admin only).
|   → Separation of concerns keeps each controller focused and small.
|--------------------------------------------------------------------------
*/
class OrganizerEventController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'eventease',
        'projectName'        => 'EventEase',
        'projectDescription' => 'Reserveringssysteem: boek tickets voor events, concerten en conferenties',
    ];

    public function index()
    {
        $events = auth()->user()->isEeAdmin()
            ? Event::orderByDesc('date')->get()
            : Event::where('user_id', auth()->id())->orderByDesc('date')->get();

        return view('projects.eventease.organizer.events.index', array_merge($this->projectData, [
            'events' => $events,
        ]));
    }

    public function create()
    {
        return view('projects.eventease.organizer.events.create', $this->projectData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'location'    => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date', 'after:now'],
            'capacity'    => ['required', 'integer', 'min:1'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = auth()->id();

        Event::create($validated);

        return redirect()->route('eventease.organizer.events.index')
                         ->with('success', 'Event created!');
    }

    public function edit(Event $event)
    {
        $this->ownershipCheck($event);

        return view('projects.eventease.organizer.events.edit', array_merge($this->projectData, [
            'event' => $event,
        ]));
    }

    public function update(Request $request, Event $event)
    {
        $this->ownershipCheck($event);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'location'    => ['required', 'string', 'max:255'],
            'date'        => ['required', 'date', 'after:now'],
            'capacity'    => ['required', 'integer', 'min:1'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $event->update($validated);

        return redirect()->route('eventease.organizer.events.index')
                         ->with('success', 'Event updated!');
    }

    public function destroy(Event $event)
    {
        $this->ownershipCheck($event);

        $event->delete();

        return redirect()->route('eventease.organizer.events.index')
                         ->with('success', 'Event deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    private function ownershipCheck(Event $event): void
    {
        if (! auth()->user()->isEeAdmin() && $event->user_id !== auth()->id()) {
            abort(403, 'You do not own this event.');
        }
    }
}
