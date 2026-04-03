<?php

namespace App\Http\Controllers\EventEase;

use App\Http\Controllers\Controller;
use App\Models\EventEase\Event;
use App\Models\EventEase\Reservation;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ReservationController
|--------------------------------------------------------------------------
| index()  → shows the logged-in user's own reservations ("My Tickets")
|
| store()  → books the user onto an event
|   Overbooking check: remainingCapacity() is called BEFORE inserting.
|   If the event is full, redirect back with an error.
|   If the user already has a cancelled reservation, UPDATE it back to
|   'confirmed' (avoids unique-constraint violation on re-booking).
|   Otherwise create a new reservation row.
|
| update() → toggles between 'confirmed' and 'cancelled'
|   Cancel  = set status to 'cancelled' (keeps row for audit history)
|   Rebook  = set status to 'confirmed'
|
| Why UPDATE instead of DELETE + re-INSERT for cancellation?
|   → The ee_reservations table has unique(user_id, event_id).
|   → If you DELETE and re-INSERT you still get the same result, but
|     UPDATE is simpler and preserves the original created_at timestamp.
|   → It also prevents a tiny race-condition window where two rapid
|     requests could both pass the uniqueness check before either commits.
|--------------------------------------------------------------------------
*/
class ReservationController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'eventease',
        'projectName'        => 'EventEase',
        'projectDescription' => 'Reserveringssysteem: boek tickets voor events, concerten en conferenties',
    ];

    public function index()
    {
        $reservations = auth()->user()
            ->reservations()
            ->with('event')
            ->latest()
            ->get();

        return view('projects.eventease.reservations.index', array_merge($this->projectData, [
            'reservations' => $reservations,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer', 'exists:ee_events,id'],
            'seats'    => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $event = Event::findOrFail($validated['event_id']);

        // Overbooking check
        if ($event->remainingCapacity() < $validated['seats']) {
            return back()->withErrors(['seats' => 'Not enough seats available. Remaining: ' . $event->remainingCapacity()]);
        }

        // Check if the user already has a (cancelled) reservation for this event
        $existing = Reservation::where('user_id', auth()->id())
                                ->where('event_id', $event->id)
                                ->first();

        if ($existing) {
            // Re-book: update cancelled reservation back to confirmed
            $existing->update([
                'seats'  => $validated['seats'],
                'status' => Reservation::STATUS_CONFIRMED,
            ]);
        } else {
            Reservation::create([
                'user_id'  => auth()->id(),
                'event_id' => $event->id,
                'seats'    => $validated['seats'],
                'status'   => Reservation::STATUS_CONFIRMED,
            ]);
        }

        return redirect()->route('eventease.reservations.index')
                         ->with('success', 'Reservation confirmed!');
    }

    public function update(Request $request, Reservation $reservation)
    {
        // Authorisation: users can only modify their own reservations
        abort_if($reservation->user_id !== auth()->id(), 403);

        $action = $request->input('action');

        if ($action === 'cancel') {
            $reservation->update(['status' => Reservation::STATUS_CANCELLED]);
            return back()->with('success', 'Reservation cancelled.');
        }

        if ($action === 'rebook') {
            // Re-check capacity before restoring
            if ($reservation->event->remainingCapacity() < $reservation->seats) {
                return back()->withErrors(['seats' => 'Event is now full — rebooking not possible.']);
            }
            $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);
            return back()->with('success', 'Reservation restored!');
        }

        return back();
    }
}
