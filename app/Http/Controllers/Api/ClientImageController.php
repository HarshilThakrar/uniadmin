<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientImage;

class ClientImageController extends Controller
{
    public function index()
    {
        $images = ClientImage::where('is_active', true)
                             ->orderBy('sort_order', 'asc')
                             ->latest()
                             ->get();

        // Transform the images array so we return the full URL to the public disk
        $images = $images->map(function ($clientImage) {
            return [
                'id' => $clientImage->id,
                'image_url' => asset('storage/' . $clientImage->image),
                'sort_order' => $clientImage->sort_order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }
}
