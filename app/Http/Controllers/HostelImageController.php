<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HostelImageController extends Controller
{
    public function show(string $hostel, string $file)
    {
        $path = 'hostels/' . $hostel . '/' . $file;

        if (!Storage::disk('public_root')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public_root')->path($path);
        $mime = Storage::disk('public_root')->mimeType($path);

        $response = new BinaryFileResponse($fullPath);
        $response->headers->set('Content-Type', $mime);
        $response->setCache(['public' => true, 'max_age' => 86400]);

        return $response;
    }
}