<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    public function compressAndResize($file, $targetSize)
    {
        $image = Image::make($file->getPathname());

        // Réduire la qualité pour les fichiers JPEG et PNG
        if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            $image->resize(1280, 960);
        }

        return $image;
    }
}
