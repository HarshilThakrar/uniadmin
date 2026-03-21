<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaqQuestion;

class FaqQuestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $faqQuestion = FaqQuestion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question submitted successfully.',
            'data' => $faqQuestion
        ], 201);
    }
}
