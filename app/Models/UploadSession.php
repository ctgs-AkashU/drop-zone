<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\File;

class UploadSession extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'custom_id', 'message', 'file_count', 'ip_address'];

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
