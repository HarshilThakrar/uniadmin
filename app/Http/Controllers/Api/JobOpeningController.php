<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Mail\JobApplicationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class JobOpeningController extends Controller
{
    public function index()
    {
        return response()->json(JobOpening::where('is_active', true)->orderBy('created_at', 'desc')->get());
    }

    public function apply(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        try {
            $data = $request->only(['name', 'email', 'phone', 'position']);
            
            // Store the resume temporarily to attach it
            $resume = $request->file('resume');
            $path = $resume->store('temp_resumes');
            $fullPath = storage_path('app/private/' . $path);

            // The email to receive notifications
            $toEmail = env('MAIL_TO_ADDRESS', 'admin@example.com');
            
            // Send the email
            Mail::to($toEmail)->send(new JobApplicationMail($data, $fullPath));

            // Optional: delete the temporary file after sending
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json(['message' => 'Application submitted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Job Application Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to submit application. Please try again later.'], 500);
        }
    }
}
