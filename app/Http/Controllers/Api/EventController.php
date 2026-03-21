<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('year', 'desc')
            ->get();
            
        return response()->json($events);
    }
}
