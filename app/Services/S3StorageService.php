<?php

namespace App\Services;

use App\Models\File;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class S3StorageService
{
    public const MAX_STORAGE_BYTES = 5 * 1024 ** 3; // 5 GB
    public const WARNING_PERCENT = 80;

    protected $s3Client;
    protected $bucket;

    public function __construct()
    {
        $this->s3Client = Storage::disk('s3')->getClient();
        $this->bucket = config('filesystems.disks.s3.bucket');
    }

    /** Use DB sum instead of scanning S3 (much faster & accurate with your setup) */
    public function getUsedBytes(): int
    {
        return Cache::remember('s3_storage_used_bytes', now()->addMinutes(5), function () {
            return File::sum('size');
        });
    }

    public function getStorageStats(): array
    {
        $usedBytes = $this->getUsedBytes();
        $totalBytes = self::MAX_STORAGE_BYTES;
        $freeBytes = $totalBytes - $usedBytes;
        $percentage = $totalBytes > 0 ? round(($usedBytes / $totalBytes) * 100, 2) : 0;

        $isNearLimit = $percentage >= self::WARNING_PERCENT;
        $isFull = $usedBytes >= $totalBytes;

        return [
            'used'          => $this->formatBytes($usedBytes),
            'used_bytes'    => $usedBytes,
            'free'          => $this->formatBytes($freeBytes > 0 ? $freeBytes : 0),
            'free_bytes'    => $freeBytes > 0 ? $freeBytes : 0,
            'total'         => '5 GB',
            'total_bytes'   => $totalBytes,
            'percentage'    => $percentage,
            'is_near_limit' => $isNearLimit,
            'is_full'       => $isFull,
        ];
    }

    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function canUpload(): bool
    {
        return ! $this->getStorageStats()['is_full'];
    }

    public function shouldWarn(): bool
    {
        return $this->getStorageStats()['is_near_limit'];
    }
}