<?php

// One-off script: generates placeholder images referenced by seeder/views.
// Run: php scripts/make-placeholders.php

require __DIR__.'/../vendor/autoload.php';

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

$manager = new ImageManager(new Driver());

function oceanPlaceholder(ImageManager $manager, int $w, int $h, string $text): \Intervention\Image\Interfaces\ImageInterface
{
    $img = $manager->createImage($w, $h)->fill('#04293A');

    // Simple two-band gradient effect: darker top, lighter bottom
    $img->drawRectangle(function ($rect) use ($w, $h) {
        $rect->size($w, (int) ($h / 2));
        $rect->at(0, 0);
        $rect->background('#041C32');
    });

    // Decorative bubbles
    mt_srand(42);
    for ($i = 0; $i < 14; $i++) {
        $cx = mt_rand(0, $w);
        $cy = mt_rand(0, $h);
        $rad = mt_rand(4, (int) max(6, $w / 40));
        $img->drawCircle(function ($circle) use ($cx, $cy, $rad) {
            $circle->at($cx, $cy);
            $circle->radius($rad);
            $circle->border('rgba(236, 179, 101, 0.35)', 2);
        });
    }

    $img->text($text, (int) ($w / 2), (int) ($h / 2), function ($font) {
        $font->size(5); // GD built-in font size (1-5) when no font file given
        $font->color('#ECB365');
        $font->align('center', 'center');
    });

    return $img;
}

@mkdir(__DIR__.'/../public/images/products', 0755, true);

oceanPlaceholder($manager, 1600, 900, 'Finity Fish Store')
    ->save(__DIR__.'/../public/images/hero-poster.jpg', quality: 82);
echo "Created public/images/hero-poster.jpg\n";

oceanPlaceholder($manager, 800, 800, 'Photo coming soon')
    ->save(__DIR__.'/../public/images/products/placeholder.jpg', quality: 82);
echo "Created public/images/products/placeholder.jpg\n";
