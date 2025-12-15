<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UploadSession;
use App\Services\S3StorageService;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = UploadSession::withCount('files')->latest()->paginate(10);
        return view('admin.uploads.index', compact('uploads'));
    }

    public function show(UploadSession $upload)
    {
        $upload->load('files');
        return view('admin.uploads.show', compact('upload'));
    }

    public function destroy(UploadSession $upload)
    {
        ini_set('max_execution_time', -1);
        set_time_limit(0);
        // Delete each file strictly
        foreach ($upload->files as $file) {
            $this->destroyFile($file);
        }
        
        // Session deletion depends on files being gone
        if ($upload->files()->count() === 0) {
            $upload->delete();
            return redirect()->route('admin.uploads.index')->with('success', 'Upload session deleted permanently.');
        }

        return redirect()->route('admin.uploads.index')->with('error', 'Some files could not be deleted.');
    }

    public function destroyFile(\App\Models\File $file)
    {
        ini_set('max_execution_time', -1);
        set_time_limit(0);
        
        // 1. Delete specific file from S3
        if (Storage::disk('s3')->exists($file->path)) {
            Storage::disk('s3')->delete($file->path);
        }

        // 2. Check if parent directory is empty
        $directory = dirname($file->path);
        $filesInDir = Storage::disk('s3')->files($directory);
        
        if (empty($filesInDir)) {
             // Only delete directory if it's empty
             Storage::disk('s3')->deleteDirectory($directory);
        }

        // 3. Delete from DB
        // We assume S3 deletion succeeded or check existence again if strictly needed, 
        // but typically delete() is enough.
        $file->delete();
             
        if (request()->routeIs('admin.files.destroy')) {
             return back()->with('success', 'File deleted permanently.');
        }
             
        return true;
    }

    public function download(UploadSession $upload, \App\Services\ZipService $zipService)
    {
        ini_set('max_execution_time', -1);
        set_time_limit(0);
        try {
            $zipPath = $zipService->createZip($upload);
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ZIP: ' . $e->getMessage());
        }
    }

    public function downloadFile(\App\Models\File $file)
    {
        ini_set('max_execution_time', -1);
        set_time_limit(0);
        if (Storage::disk('s3')->exists($file->path)) {
            return Storage::disk('s3')->download($file->path, $file->original_name);
        }
        return back()->with('error', 'File not found on server.');
    }

    public function authorize()
    {
        $storage = app(S3StorageService::class);

        // Full? Block everyone
        if (! $storage->canUpload()) {
            return false;
        }

        // 80%+? Only allow admins
        if ($storage->shouldWarn() && ! auth()->user()?->is_admin) { // adjust to your admin check
            return false;
        }

        return true;
    }

    public function failedAuthorization()
    {
        $storage = app(S3StorageService::class);
        $stats = $storage->getStorageStats();

        if ($stats['is_full']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'files' => 'Storage limit reached (5 GB). Contact administrator.'
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'files' => 'Storage is over 80% full. Uploads are restricted. Contact admin.'
        ]);
    }

}
