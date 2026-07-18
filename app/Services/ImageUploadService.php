<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadService
{
    public function storeOptimized(UploadedFile $file, string $directory, ?string $oldPublicPath = null): string
    {
        $filename = $directory.'/'.Str::uuid().'.jpg';
        $absolutePath = storage_path('app/public/'.$filename);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        Image::decode($file)
            ->scaleDown(width: 1200)
            ->save($absolutePath, quality: 85);

        $this->deletePublicPath($oldPublicPath);

        return '/storage/'.$filename;
    }

    public function deletePublicPath(?string $publicPath): void
    {
        if (! $publicPath || ! str_starts_with($publicPath, '/storage/')) {
            return;
        }

        $relative = Str::after($publicPath, '/storage/');
        if ($relative && Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
