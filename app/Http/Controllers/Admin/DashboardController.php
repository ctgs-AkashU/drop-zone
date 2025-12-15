<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UploadSession;
use App\Models\File;
use App\Services\S3StorageService;

class DashboardController extends Controller
{
    public function index()
    {
        $s3Service = app(\App\Services\S3StorageService::class);

        return view('admin.dashboard', [
            'stats' => [
                'total_sessions' => UploadSession::count(),
                'total_files'    => File::count(),
                'recent_uploads' => UploadSession::withCount('files')->latest()->take(10)->get(),
            ],
            'storage' => $s3Service->getStorageStats(),
        ]);
    }
}
