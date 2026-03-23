<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Mail\JobApplicationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            // Store the resume
            $resume = $request->file('resume');
            // Store in 'public' disk under 'resumes' folder
            $resumePath = $resume->store('resumes', 'public');
            $fullPath = Storage::disk('public')->path($resumePath); // Get the full path to the stored file

            // The email to receive notifications
            $toEmail = env('MAIL_TO_ADDRESS', 'admin@example.com');

            try {
                // Send the email
                Mail::to($toEmail)->send(new JobApplicationMail($data, $fullPath));
            }
            catch (\Exception $mailException) {
                Log::warning('Job Application Mail Failed for ' . $data['email'] . ': ' . $mailException->getMessage());
                // If mail fails, we still consider the application submitted, but notify about the email issue.
                // The resume is already saved.
                return response()->json([
                    'message' => 'Application submitted, but email notification failed. Please check server SMTP settings.',
                    'status' => 'partial_success'
                ], 200);
            }

            // Optional: If you want to delete the resume after sending the email,
            // you might reconsider as it's the only record of the application.
            // If the mail failed, the resume is still there.
            // If mail succeeded, the resume is still there.
            // The original instruction had a temporary file, but the edit changed it to 'public' storage.
            // For now, we'll leave the resume in public storage as it's a record of the application.

            return response()->json(['message' => 'Application submitted successfully'], 200);
        }
        catch (\Exception $e) {
            Log::error('Job Application Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to submit application. ' . $e->getMessage()], 500);
        }
    }
}
