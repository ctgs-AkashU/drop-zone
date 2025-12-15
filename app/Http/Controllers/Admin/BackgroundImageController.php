<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BackgroundImage;
use Illuminate\Support\Facades\Storage;

class BackgroundImageController extends Controller
{
    public function index()
    {
        $images = BackgroundImage::latest()->get();
        return view('admin.backgrounds.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
        ]);

        $path = $request->file('image')->store('backgrounds', 'public');

        BackgroundImage::create([
            'path' => $path,
            'is_active' => true,
        ]);

        return back()->with('success', 'Background image uploaded.');
    }

    public function destroy(BackgroundImage $background)
    {
        Storage::disk('public')->delete($background->path);
        $background->delete();

        return back()->with('success', 'Image deleted.');
    }
}
