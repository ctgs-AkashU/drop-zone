<?php

namespace App\Services;

use App\Models\UploadSession;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;

class ZipService
{
{
    public function createZip(UploadSession $session)
    {
        ini_set('max_execution_time', -1);
        set_time_limit(0);
        $zipFileName = 'download-' . $session->session_id . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($session->files as $file) {
                // Get file content from S3
                if (Storage::disk('s3')->exists($file->path)) {
                    $content = Storage::disk('s3')->get($file->path);
                    $zip->addFromString($file->original_name, $content);
                }
            }
            $zip->close();
        }

        return $zipPath;
    }
}
