<?php
// app/Services/MediaUploadService.php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;

/**
 * Service de gestion des médias pour la messagerie
 * Gère l'upload, le traitement et la validation des fichiers
 */
class MediaUploadService
{
    /**
     * Types de fichiers autorisés
     */
    const ALLOWED_IMAGES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    const ALLOWED_VIDEOS = ['video/mp4', 'video/webm', 'video/quicktime'];
    const ALLOWED_FILES = [
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain', 'application/zip', 'application/x-rar-compressed'
    ];
    const ALLOWED_STICKERS = ['image/png', 'image/webp', 'image/gif'];
    
    /**
     * Tailles maximales (en octets)
     */
    const MAX_IMAGE_SIZE = 5242880;   // 5 MB
    const MAX_VIDEO_SIZE = 52428800;  // 50 MB
    const MAX_FILE_SIZE = 10485760;   // 10 MB
    const MAX_STICKER_SIZE = 1048576; // 1 MB

    /**
     * Dimensions maximales pour les images
     */
    const MAX_IMAGE_WIDTH = 4096;
    const MAX_IMAGE_HEIGHT = 4096;

    /**
     * Upload et traitement d'une image
     *
     * @param UploadedFile $file
     * @param bool $generateThumbnail
     * @return array
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, bool $generateThumbnail = true): array
    {
        $this->validateFile($file, self::ALLOWED_IMAGES, self::MAX_IMAGE_SIZE);
        
        // Vérifier les dimensions
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo[0] > self::MAX_IMAGE_WIDTH || $imageInfo[1] > self::MAX_IMAGE_HEIGHT) {
            throw new \Exception('Les dimensions de l\'image sont trop grandes. Maximum: ' . self::MAX_IMAGE_WIDTH . 'x' . self::MAX_IMAGE_HEIGHT);
        }
        
        $filename = $this->generateFileName($file, 'img');
        $path = $this->storeFile($file, $filename, 'messages');
        
        $result = [
            'media_url' => Storage::disk('public')->url('messages/' . $filename),
            'media_type' => $file->getMimeType(),
            'media_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'type' => 'image',
        ];
        
        // Générer une miniature
        if ($generateThumbnail) {
            $thumbnail = $this->generateImageThumbnail($file);
            if ($thumbnail) {
                $result['thumbnail_url'] = $thumbnail;
            }
        }
        
        return $result;
    }

    /**
     * Upload et traitement d'une vidéo
     *
     * @param UploadedFile $file
     * @return array
     * @throws \Exception
     */
    public function uploadVideo(UploadedFile $file): array
    {
        $this->validateFile($file, self::ALLOWED_VIDEOS, self::MAX_VIDEO_SIZE);
        
        $filename = $this->generateFileName($file, 'video');
        $path = $this->storeFile($file, $filename, 'messages');
        
        $result = [
            'media_url' => Storage::disk('public')->url('messages/' . $filename),
            'media_type' => $file->getMimeType(),
            'media_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'type' => 'video',
            'duration' => $this->getVideoDuration($file),
        ];
        
        // Générer une miniature à partir de la vidéo
        $thumbnail = $this->generateVideoThumbnail($file);
        if ($thumbnail) {
            $result['thumbnail_url'] = $thumbnail;
        }
        
        return $result;
    }

    /**
     * Upload d'un fichier générique
     *
     * @param UploadedFile $file
     * @return array
     * @throws \Exception
     */
    public function uploadFile(UploadedFile $file): array
    {
        $this->validateFile($file, self::ALLOWED_FILES, self::MAX_FILE_SIZE);
        
        $filename = $this->generateFileName($file, 'file');
        $path = $this->storeFile($file, $filename, 'messages');
        
        return [
            'media_url' => Storage::disk('public')->url('messages/' . $filename),
            'media_type' => $file->getMimeType(),
            'media_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'type' => 'file',
        ];
    }

    /**
     * Upload d'un sticker (généralement plus petit avec transparence)
     *
     * @param UploadedFile $file
     * @return array
     * @throws \Exception
     */
    public function uploadSticker(UploadedFile $file): array
    {
        $this->validateFile($file, self::ALLOWED_STICKERS, self::MAX_STICKER_SIZE);
        
        $filename = $this->generateFileName($file, 'sticker');
        $path = $this->storeFile($file, $filename, 'messages');
        
        // Optimiser le sticker (garder la transparence)
        $this->optimizeSticker($file, storage_path('app/public/messages/' . $filename));
        
        return [
            'media_url' => Storage::disk('public')->url('messages/' . $filename),
            'media_type' => $file->getMimeType(),
            'media_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'type' => 'sticker',
        ];
    }

    /**
     * Supprime un fichier média
     *
     * @param string|null $mediaUrl
     * @return bool
     */
    public function deleteMedia(?string $mediaUrl): bool
    {
        if (!$mediaUrl) {
            return false;
        }
        
        try {
            // Extraire le chemin relatif
            $path = str_replace('/storage/', '', parse_url($mediaUrl, PHP_URL_PATH) ?? '');
            
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            
            // Supprimer également la miniature si elle existe
            $thumbnailPath = dirname($path) . '/thumbnails/' . basename($path);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du média: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère une miniature pour une image
     *
     * @param UploadedFile $file
     * @return string|null
     */
    private function generateImageThumbnail(UploadedFile $file): ?string
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            
            // Redimensionner en conservant le ratio
            $image->cover(200, 200);
            
            $thumbnailFilename = 'thumbnails/' . $this->generateFileName($file, 'thumb');
            $thumbnailPath = storage_path('app/public/messages/' . $thumbnailFilename);
            
            // Créer le dossier thumbnails s'il n'existe pas
            if (!file_exists(dirname($thumbnailPath))) {
                mkdir(dirname($thumbnailPath), 0755, true);
            }
            
            $image->toJpeg(80)->save($thumbnailPath);
            
            return Storage::disk('public')->url('messages/' . $thumbnailFilename);
        } catch (\Exception $e) {
            Log::warning('Impossible de générer la miniature: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Génère une miniature pour une vidéo
     *
     * @param UploadedFile $file
     * @return string|null
     */
    private function generateVideoThumbnail(UploadedFile $file): ?string
    {
        // Nécessite l'extension PHP-FFMpeg
        // Installation: composer require php-ffmpeg/php-ffmpeg
        
        try {
            if (!class_exists(FFMpeg::class)) {
                return null;
            }
            
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => env('FFMPEG_PATH', 'ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
                'timeout' => 3600,
                'ffmpeg.threads' => 12,
            ]);
            
            $video = $ffmpeg->open($file->getPathname());
            $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1));
            
            $thumbnailFilename = 'thumbnails/' . $this->generateFileName($file, 'video_thumb', 'jpg');
            $thumbnailPath = storage_path('app/public/messages/' . $thumbnailFilename);
            
            // Créer le dossier thumbnails s'il n'existe pas
            if (!file_exists(dirname($thumbnailPath))) {
                mkdir(dirname($thumbnailPath), 0755, true);
            }
            
            $frame->save($thumbnailPath);
            
            // Redimensionner la miniature
            $manager = new ImageManager(new Driver());
            $image = $manager->read($thumbnailPath);
            $image->cover(200, 200);
            $image->save($thumbnailPath);
            
            return Storage::disk('public')->url('messages/' . $thumbnailFilename);
        } catch (\Exception $e) {
            Log::warning('Impossible de générer la miniature vidéo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Optimise un sticker (taille et transparence)
     *
     * @param UploadedFile $file
     * @param string $destinationPath
     * @return void
     */
    private function optimizeSticker(UploadedFile $file, string $destinationPath): void
    {
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($destinationPath);
            
            // Redimensionner le sticker si trop grand (max 512x512)
            if ($image->width() > 512 || $image->height() > 512) {
                $image->resize(512, 512, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            $image->save($destinationPath);
        } catch (\Exception $e) {
            Log::warning('Optimisation du sticker impossible: ' . $e->getMessage());
        }
    }

    /**
     * Récupère la durée d'une vidéo
     *
     * @param UploadedFile $file
     * @return int|null
     */
    private function getVideoDuration(UploadedFile $file): ?int
    {
        try {
            if (!class_exists(FFMpeg::class)) {
                return null;
            }
            
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => env('FFMPEG_PATH', 'ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_PATH', 'ffprobe'),
            ]);
            
            $video = $ffmpeg->open($file->getPathname());
            $duration = $video->getStreams()->videos()->first()->get('duration');
            
            return (int) ceil($duration);
        } catch (\Exception $e) {
            Log::warning('Impossible de récupérer la durée de la vidéo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Valide le fichier
     *
     * @param UploadedFile $file
     * @param array $allowedMimeTypes
     * @param int $maxSize
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file, array $allowedMimeTypes, int $maxSize): void
    {
        if (!$file->isValid()) {
            throw new \Exception('Le fichier est corrompu ou invalide.');
        }
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Type de fichier non autorisé. Types acceptés: ' . implode(', ', $allowedMimeTypes));
        }
        
        if ($file->getSize() > $maxSize) {
            $maxSizeMB = $maxSize / 1048576;
            throw new \Exception("Le fichier est trop volumineux. Maximum: {$maxSizeMB} MB");
        }
    }

    /**
     * Génère un nom de fichier unique
     *
     * @param UploadedFile $file
     * @param string $prefix
     * @param string|null $extension
     * @return string
     */
    private function generateFileName(UploadedFile $file, string $prefix = 'file', ?string $extension = null): string
    {
        $ext = $extension ?? $file->getClientOriginalExtension();
        return sprintf('%s_%s_%s.%s', $prefix, date('Ymd_His'), Str::random(16), $ext);
    }

    /**
     * Stocke le fichier sur le disque
     *
     * @param UploadedFile $file
     * @param string $filename
     * @param string $disk
     * @return string
     */
    private function storeFile(UploadedFile $file, string $filename, string $disk): string
    {
        $path = Storage::disk('public')->putFileAs($disk, $file, $filename);
        
        if (!$path) {
            throw new \Exception('Erreur lors de l\'upload du fichier.');
        }
        
        return $path;
    }
}