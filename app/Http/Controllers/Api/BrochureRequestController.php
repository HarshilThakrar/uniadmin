<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BrochureRequest;
use Illuminate\Http\Request;

class BrochureRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:255',
            'brochure_path' => 'required|string|max:255',
        ]);

        BrochureRequest::create($validated);

        return response()->json(['message' => 'Request submitted successfully'], 201);
    }
}
