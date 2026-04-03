<?php

namespace App\Http\Controllers\EventEase;

use App\Http\Controllers\Controller;
use App\Models\EventEase\Event;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventController (public)
|--------------------------------------------------------------------------
| This controller handles the PUBLIC-facing event pages.
| No authentication required — anyone can browse events.
|
| index()  → lists all UPCOMING events (using the scopeUpcoming() scope)
| show()   → shows a single event's details and remaining capacity
|
| The $projectData array is passed to every view so the layout can:
|   • highlight the correct project in the switcher nav
|   • display the project name in the header
|   • show the correct project nav bar
|--------------------------------------------------------------------------
*/
class EventController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'eventease',
        'projectName'        => 'EventEase',
        'projectDescription' => 'Reserveringssysteem: boek tickets voor events, concerten en conferenties',
    ];

    public function index()
    {
        $events = Event::upcoming()->get();

        return view('projects.eventease.home', array_merge($this->projectData, [
            'events' => $events,
        ]));
    }

    public function show(Event $event)
    {
        return view('projects.eventease.events.show', array_merge($this->projectData, [
            'event'               => $event,
            'remainingCapacity'   => $event->remainingCapacity(),
            'userReservation'     => auth()->check()
                ? $event->reservations()->where('user_id', auth()->id())->first()
                : null,
        ]));
    }
}
