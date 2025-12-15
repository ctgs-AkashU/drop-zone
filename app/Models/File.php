<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['upload_session_id', 'original_name', 'path', 'mime_type', 'size', 'is_folder', 'folder_path'];

    public function uploadSession()
    {
        return $this->belongsTo(UploadSession::class);
    }
}
