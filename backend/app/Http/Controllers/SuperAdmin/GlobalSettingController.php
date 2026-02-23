<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GlobalSettingController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::all()->groupBy('group');
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $settings = $request->get('settings', []);

        foreach ($settings as $key => $value) {
            $setting = GlobalSetting::where('key', $key)->first();
            if ($setting) {
                // If it's an image/file, we might need special handling if it's being uploaded
                // But usually for simple dynamic settings, we just store the value
                $setting->update(['value' => $value]);
            }
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
            'key' => 'required|string'
        ]);

        $setting = GlobalSetting::where('key', $request->key)->first();
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('settings', 'public');
            $url = Storage::url($path);
            $setting->update(['value' => $url]);
            return response()->json(['url' => $url, 'message' => 'File uploaded successfully']);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
