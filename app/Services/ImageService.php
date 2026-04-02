<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Exception;

class ImageService
{
    protected array $sizes = [
        'thumbnail' => [400, 400],
        'medium' => [800, 800],
        'large' => [1920, 1920],
        'original' => null
    ];

    protected string $disk = 'public';

    public function upload(UploadedFile $file, string $path, array $options = []): array
    {
        try {
            $filename = time() . '_' . $file->hashName();
            $uploadedPaths = [];

            foreach ($this->sizes as $sizeName => $dimensions) {
                $img = Image::read($file);

                if ($dimensions) {
                    $img->scale($dimensions[0], $dimensions[1]);
                }

                $quality = $options['quality'] ?? 85;
                $format = $options['format'] ?? 'webp';

                if ($sizeName === 'original') {
                    $savePath = "{$path}/original/{$filename}";
                } else {
                    $savePath = "{$path}/{$sizeName}/{$filename}";
                }

                $img->save(Storage::disk($this->disk)->path($savePath), $quality);
                $uploadedPaths[$sizeName] = $savePath;
            }

            return [
                'success' => true,
                'paths' => $uploadedPaths,
                'filename' => $filename,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk($this->disk)->exists($path)) {
                Storage::disk($this->disk)->delete($path);
            }
        }
    }

    public function getThumbnailUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }
}