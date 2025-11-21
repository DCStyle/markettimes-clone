<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected ImageManager $manager;

    protected array $sizes = [
        'thumbnail' => ['width' => 540, 'height' => 360],
        'medium' => ['width' => 800, 'height' => 533],
        'large' => ['width' => 1200, 'height' => 800],
    ];

    protected int $quality = 85;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process uploaded file and generate optimized versions
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Storage path (e.g., 'articles')
     * @return array Array of generated file paths
     */
    public function processUpload($file, string $path = 'articles'): array
    {
        $paths = [];

        // Generate unique filename
        $filename = time() . '_' . uniqid();
        $extension = $file->getClientOriginalExtension();

        // Store original image
        $originalPath = $file->storeAs("images/{$path}", "{$filename}.{$extension}", 'public');
        $paths['original'] = $originalPath;

        // Load image with Intervention
        $image = $this->manager->read($file->getRealPath());

        // Generate different sizes
        foreach ($this->sizes as $sizeName => $dimensions) {
            // Create resized version
            $resized = $image->cover($dimensions['width'], $dimensions['height']);

            // Save as JPEG/PNG
            $sizePath = "images/{$path}/{$filename}_{$sizeName}.{$extension}";
            Storage::disk('public')->put(
                $sizePath,
                $resized->toJpeg($this->quality)
            );
            $paths[$sizeName] = $sizePath;

            // Save as WebP
            $webpPath = "images/{$path}/{$filename}_{$sizeName}.webp";
            Storage::disk('public')->put(
                $webpPath,
                $resized->toWebp($this->quality)
            );
            $paths["{$sizeName}_webp"] = $webpPath;
        }

        // Generate WebP version of original
        $originalWebpPath = "images/{$path}/{$filename}.webp";
        Storage::disk('public')->put(
            $originalWebpPath,
            $image->toWebp($this->quality)
        );
        $paths['original_webp'] = $originalWebpPath;

        return $paths;
    }

    /**
     * Resize image to specific dimensions
     *
     * @param mixed $image Image instance
     * @param int $width
     * @param int $height
     * @return mixed Resized image
     */
    public function resize($image, int $width, int $height)
    {
        return $image->cover($width, $height);
    }

    /**
     * Convert image to WebP format
     *
     * @param mixed $image
     * @param int $quality
     * @return mixed WebP encoded image
     */
    public function convertToWebP($image, int $quality = 85)
    {
        return $image->toWebp($quality);
    }

    /**
     * Optimize image quality
     *
     * @param mixed $image
     * @param int $quality
     * @return mixed Optimized image
     */
    public function optimize($image, int $quality = 85)
    {
        return $image->toJpeg($quality);
    }

    /**
     * Delete all image versions
     *
     * @param array $paths Array of image paths to delete
     * @return void
     */
    public function deleteImages(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Get responsive image HTML attributes
     *
     * @param array $paths Array of image paths
     * @param string $alt Alt text
     * @param string $class CSS classes
     * @return array HTML attributes for responsive image
     */
    public function getResponsiveImageData(array $paths, string $alt = '', string $class = ''): array
    {
        return [
            'sources' => [
                [
                    'srcset' => isset($paths['large_webp']) ? Storage::url($paths['large_webp']) : null,
                    'media' => '(min-width: 1024px)',
                    'type' => 'image/webp',
                ],
                [
                    'srcset' => isset($paths['medium_webp']) ? Storage::url($paths['medium_webp']) : null,
                    'media' => '(min-width: 768px)',
                    'type' => 'image/webp',
                ],
                [
                    'srcset' => isset($paths['thumbnail_webp']) ? Storage::url($paths['thumbnail_webp']) : null,
                    'type' => 'image/webp',
                ],
            ],
            'fallback' => [
                'src' => isset($paths['thumbnail']) ? Storage::url($paths['thumbnail']) : (isset($paths['original']) ? Storage::url($paths['original']) : ''),
                'alt' => $alt,
                'class' => $class,
                'loading' => 'lazy',
            ],
        ];
    }
}
