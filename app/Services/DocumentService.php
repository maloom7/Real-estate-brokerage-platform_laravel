<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class DocumentService
{
    protected string $privateDisk = 'private';
    protected array $allowedMimes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'tiff'];
    protected int $maxSize = 52428800;

    public function upload(UploadedFile $file, string $path, bool $isConfidential = false): array
    {
        try {
            $this->validateFile($file);

            $encryptedName = Str::uuid() . '_' . $file->hashName();
            $disk = $isConfidential ? $this->privateDisk : 'public';
            $storedPath = $file->storeAs($path, $encryptedName, $disk);

            return [
                'success' => true,
                'path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'encrypted_name' => $encryptedName,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => $disk,
                'is_confidential' => $isConfidential
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function validateFile(UploadedFile $file): void
    {
        if ($file->getSize() > $this->maxSize) {
            throw new Exception('حجم الملف يتجاوز الحد المسموح (50MB)');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedMimes)) {
            throw new Exception('صيغة الملف غير مسموحة');
        }
    }

    public function download(string $path, string $originalName, bool $isConfidential = false)
    {
        $disk = $isConfidential ? $this->privateDisk : 'public';
        
        if (!Storage::disk($disk)->exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk($disk)->download($path, $originalName);
    }

    public function delete(string $path, bool $isConfidential = false): bool
    {
        $disk = $isConfidential ? $this->privateDisk : 'public';
        return Storage::disk($disk)->delete($path);
    }
}