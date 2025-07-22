<?php

namespace App\Helpers;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    /**
     * Redimensionne une image et retourne une instance Intervention Image.
     * Utilise un placeholder par défaut si l'image source est invalide ou un SVG.
     */
    public static function resize_image($image_path, $width, $height)
    {
        $full_path = storage_path('app/public/' . $image_path);

        // Utilise un PNG/JPG par défaut si l'image n'existe pas ou est un SVG (incompatible avec le driver GD)
        if (empty($image_path) || !File::exists($full_path) || (File::exists($full_path) && strtolower(File::extension($full_path)) === 'svg')) {
            $full_path = public_path('images/default-avatar.png'); // Assure-toi d'avoir une image default-avatar.png
        }

        return Image::make($full_path)->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        });
    }

    /**
     * Génère une balise HTML <img> avec l'image encodée en base64 (Data-URI).
     * Cela évite de devoir créer des routes dédiées pour chaque image.
     */
    public static function displayProfileImage($logo, $name, $class = '', $attributes = [])
    {
        $image = self::resize_image($logo, 150, 150);

        // Convertit le tableau d'attributs en chaîne de caractères HTML
        $attributeString = '';
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $attributeString .= e($key) . '="' . e($value) . '" ';
            }
        }
        
        // Encode l'image en Data-URL
        $dataUri = $image->encode('data-url')->encoded;

        return '<img src="' . $dataUri . '" class="' . e($class) . '" alt="' . e($name) . '" ' . $attributeString . '>';
    }
}

