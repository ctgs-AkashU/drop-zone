<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadSession;
use App\Models\BackgroundImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserUploadController extends Controller
{
    public function index()
    {
        $backgrounds = BackgroundImage::where('is_active', true)->get();
        return view('welcome', compact('backgrounds'));
    }

    public function store(Request $request)
    {
        Log::info("UPLOAD STARTED", [
            'ip' => $request->ip(),
            'session_id' => $request->session_id,
            'original_name' => $request->file('file')?->getClientOriginalName() ?? null
        ]);

        try {
            ini_set('max_execution_time', -1);
            set_time_limit(0);

            $request->validate([
                'file' => 'required|file',
                'session_id' => 'required|string',
            ]);

            $file = $request->file('file');
            $sessionId = $request->input('session_id');
            $bucketPath = env('BUCKET_PATH', 'sharda-uat');

            // Create base session folder
            $baseDir = $bucketPath . '/' . $sessionId;
            Storage::disk('s3')->makeDirectory($baseDir);

            $isChunked = $request->has('dzuuid');
            $uuid = $request->input('dzuuid');

            if ($isChunked) {
                return $this->handleChunkedUpload($request, $file, $sessionId, $bucketPath, $uuid);
            } else {
                return $this->handleRegularUpload($request, $file, $sessionId, $bucketPath);
            }

        } catch (\Throwable $e) {
            Log::error("UPLOAD FAILED", [
                'message' => $e->getMessage(),
                'session_id' => $request->session_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Upload failed.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function handleChunkedUpload($request, $file, $sessionId, $bucketPath, $uuid)
    {
        $chunkIndex = $request->input('dzchunkindex');
        $totalChunks = $request->input('dztotalchunkcount');
        $tempPath = storage_path('app/chunks/' . $uuid);

        // Create temp folder
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        // Append chunk
        $handle = fopen($tempPath, 'ab');
        $chunk = fopen($file->getPathname(), 'rb');
        if ($chunk) {
            stream_copy_to_stream($chunk, $handle);
            fclose($chunk);
        }
        fclose($handle);

        // Last chunk → assemble and upload
        if ($chunkIndex + 1 == $totalChunks) {
            $relativePath = trim($request->input('relative_path', ''), "/\\");
            $originalName = $file->getClientOriginalName();

            // === DETERMINE FINAL S3 KEY ===
            if (!empty($relativePath) && !in_array($relativePath, ['undefined', 'null', null])) {
                // If user uploaded a folder and this is a directory placeholder → skip file upload
                if (str_ends_with($relativePath, '/')) {
                    @unlink($tempPath);
                    @rmdir(dirname($tempPath));
                    return response()->json(['success' => true]); // just a folder
                }
                $s3Key = $bucketPath . '/' . $sessionId . '/' . $relativePath;
            } else {
                $s3Key = $bucketPath . '/' . $sessionId . '/' . $originalName;
            }

            // Safety: never upload only real files
            if (substr($s3Key, -1) === '/') {
                @unlink($tempPath);
                @rmdir(dirname($tempPath));
                return response()->json(['success' => true]);
            }

            // Ensure parent directory exists
            $directory = dirname($s3Key);
            if ($directory !== '.' && $directory !== $bucketPath . '/' . $sessionId) {
                Storage::disk('s3')->makeDirectory($directory);
            }

            // FINAL UPLOAD — NO ACL, NO VISIBILITY STRING → works on ACL-disabled buckets
            $stream = fopen($tempPath, 'rb');
            Storage::disk('s3')->put($s3Key, $stream);
            fclose($stream);

            // Cleanup temp chunk
            @unlink($tempPath);
            @rmdir(dirname($tempPath));

            $this->saveFileRecord(
                $sessionId,
                $request->ip(),
                $originalName,
                $s3Key,
                $file->getClientMimeType(),
                $request->input('dztotalfilesize')
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true, 'chunk_received' => true]);
    }

    private function handleRegularUpload($request, $file, $sessionId, $bucketPath)
    {
        $relativePath = trim($request->input('relative_path', ''), "/\\");
        $originalName = $file->getClientOriginalName();

        if (!empty($relativePath) && !in_array($relativePath, ['undefined', 'null', null])) {
            if (str_ends_with($relativePath, '/')) {
                $dir = $bucketPath . '/' . $sessionId . '/' . trim($relativePath, '/');
                Storage::disk('s3')->makeDirectory($dir);
                return response()->json(['success' => true]);
            }
            $s3Key = $bucketPath . '/' . $sessionId . '/' . $relativePath;
        } else {
            $s3Key = $bucketPath . '/' . $sessionId . '/' . $originalName;
        }

        // Prevent uploading folder placeholder
        if (substr($s3Key, -1) === '/') {
            return response()->json(['success' => true]);
        }

        $directory = dirname($s3Key);
        if ($directory !== '.' && !Storage::disk('s3')->exists($directory . '/')) {
            Storage::disk('s3')->makeDirectory($directory);
        }

        // For regular upload, use putFileAs (it also respects config visibility)
        // OR use simple put() like chunked version
        Storage::disk('s3')->putFileAs(
            dirname($s3Key),
            $file,
            basename($s3Key)
        );

        $this->saveFileRecord(
            $sessionId,
            $request->ip(),
            $originalName,
            $s3Key,
            $file->getMimeType(),
            $file->getSize()
        );

        return response()->json(['success' => true]);
    }

    private function saveFileRecord($sessionId, $ip, $name, $path, $mime, $size)
    {
        $session = UploadSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ip,
                'custom_id' => 'TR-' . strtoupper(Str::random(8)),
            ]
        );

        $session->files()->create([
            'original_name' => $name,
            'path' => $path,
            'mime_type' => $mime ?? 'application/octet-stream',
            'size' => $size ?? 0,
        ]);

        $session->increment('file_count');
    }

    // ... complete() and success() methods remain unchanged
    public function complete(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'nullable|string|max:1000',
        ]);

        $session = UploadSession::where('session_id', $request->session_id)->first();

        if (!$session) {
            return redirect()->route('home')->with('error', 'Invalid session.');
        }

        $session->message = $request->message;
        $session->completed_at = now();
        $session->save();

        return redirect()->route('upload.success', $session->session_id);
    }

    public function success($sessionId)
    {
        $session = UploadSession::with('files')->where('session_id', $sessionId)->firstOrFail();
        return view('upload.success', compact('session'));
    }
}