<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageVisit;

class AnalyticsController extends Controller
{
    public function trackVisit(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string|max:2048',
        ]);

        $visit = PageVisit::create([
            'url' => $validated['url'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'id' => $visit->id]);
    }

    public function leavePage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $visit = PageVisit::find($request->id);
        if ($visit) {
            $visit->update(['is_active' => false]);
        }

        return response()->json(['success' => true]);
    }
}
