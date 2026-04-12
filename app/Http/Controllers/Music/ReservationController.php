<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\Music\Reservation;
use App\Models\Music\Workshop;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ReservationController (MusicHub)
|--------------------------------------------------------------------------
| Only one method: store().
| The user must be logged in (route has 'auth' middleware) — no extra check.
|
| Guards in store():
|   1. Workshop exists         → route-model binding handles this (404)
|   2. Workshop not full       → check reservations count vs capacity
|   3. No duplicate            → check if user already reserved this workshop
|
| Snapshot pattern:
|   User data (name, email, phone, music_experience) is COPIED from the
|   authenticated user into the reservation at the time of booking.
|   The user's profile may change later — the reservation remembers what
|   it was at the moment of clicking "Reserve".
|--------------------------------------------------------------------------
*/

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'workshop_id' => 'required|exists:music_workshops,id',
        ]);

        $workshop = Workshop::withCount('reservations')->findOrFail($request->workshop_id);
        $user     = auth()->user();

        // Guard 1: workshop must not be full
        if ($workshop->reservations_count >= $workshop->capacity) {
            return redirect()->back()->with('error', 'This workshop is fully booked.');
        }

        // Guard 2: user must not have already reserved this workshop
        $alreadyBooked = Reservation::where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->exists();

        if ($alreadyBooked) {
            return redirect()->back()->with('error', 'You have already reserved a spot in this workshop.');
        }

        // Snapshot: copy user profile data into the reservation
        Reservation::create([
            'user_id'          => $user->id,
            'workshop_id'      => $workshop->id,
            'full_name'        => $user->name,
            'email'            => $user->email,
            'phone'            => $user->music_phone,
            'music_experience' => $user->music_experience,
        ]);

        return redirect()->route('music.workshops.show', $workshop)
            ->with('success', 'Your spot has been reserved! See you at the workshop.');
    }

    public function destroy(Reservation $reservation)
    {
        // Make sure the logged-in user owns this reservation
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $workshop = $reservation->workshop;
        $reservation->delete();

        return redirect()->route('music.workshops.show', $workshop)
            ->with('success', 'Your reservation has been cancelled. The spot is now available again.');
    }
}
