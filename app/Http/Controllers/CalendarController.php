<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Calendar/Index', [
            'events' => Inertia::defer(fn () => $this->calendarService->getCalendarEvents(auth()->user()))
        ]);
    }
}
