<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $query = Upload::query();
        
        // Filter by search query
        if ($request->has('search') && $request->search) {
            $query->where('file_original_name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('limit') ?? 24;
        return $query->latest()->paginate($perPage);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $type = $this->getFileType($extension);
            
            // Store under vendor directory
            $path = $file->store('uploads/' . auth()->id(), 'public');
            $url = Storage::disk('public')->url($path);
            
            $upload = Upload::create([
                'vendor_id' => auth()->id(),
                'file_name' => basename($path),
                'file_original_name' => $originalName,
                'extension' => $extension,
                'type' => $type,
                'file_size' => $size,
                'file_path' => $path,
                'file_url' => $url,
            ]);

            return response()->json([
                'message' => 'File uploaded successfully',
                'data' => $upload,
                'status' => 201
            ], 201);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function destroy($id)
    {
        $upload = Upload::where('id', $id)->firstOrFail();
        
        // Security check
        if ($upload->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }

    private function getFileType($extension)
    {
        $images = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        $videos = ['mp4', 'webm', 'ogg', 'avi', 'mov'];
        $docs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

        $ext = strtolower($extension);
        if (in_array($ext, $images)) return 'image';
        if (in_array($ext, $videos)) return 'video';
        if (in_array($ext, $docs)) return 'document';
        
        return 'other';
    }
}

