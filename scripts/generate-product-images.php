<?php

/**
 * Generate unique product images for every catalog fish and update DB paths.
 * Run: php scripts/generate-product-images.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

$catalog = require database_path('data/product_catalog.php');
$manager = new ImageManager(new Driver());

$categoryColors = [
    'Goldfish' => ['#7A1F1F', '#C45C26', '#F0A500'],
    'Betta' => ['#4A0E4E', '#9B1D9B', '#E85D75'],
    'Livebearers' => ['#0B3D91', '#2E8BC0', '#B1D4E0'],
    'Tetra' => ['#004D40', '#00796B', '#26A69A'],
    'Barb' => ['#BF360C', '#E65100', '#FFB74D'],
    'Gourami' => ['#1A237E', '#3949AB', '#7986CB'],
    'Cichlid' => ['#33691E', '#689F38', '#C0CA33'],
    'Catfish/Bottom Dweller' => ['#37474F', '#607D8B', '#B0BEC5'],
    'Shark Type' => ['#212121', '#546E7A', '#90A4AE'],
    'Nano Fish' => ['#880E4F', '#C2185B', '#F48FB1'],
    'Premium/Rare' => ['#4A148C', '#7B1FA2', '#CE93D8'],
    'Invertebrate' => ['#B71C1C', '#E53935', '#FFCDD2'],
];

$outDir = public_path('images/products');
if (! is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

function drawFishShape($img, int $cx, int $cy, int $bodyW, int $bodyH, string $bodyColor, string $finColor, string $eyeColor): void
{
    // Body (ellipse via filled circles approximation + polygon tail)
    $img->drawEllipse(function ($e) use ($cx, $cy, $bodyW, $bodyH, $bodyColor) {
        $e->size($bodyW, $bodyH);
        $e->at($cx, $cy);
        $e->background($bodyColor);
    });

    // Tail (triangle behind body)
    $tailTipX = $cx - (int) ($bodyW * 0.75);
    $img->drawPolygon(function ($p) use ($cx, $cy, $bodyW, $bodyH, $tailTipX, $finColor) {
        $bodyLeft = $cx - (int) ($bodyW / 2) + 4;
        $p->point($bodyLeft, $cy);
        $p->point($tailTipX, $cy - (int) ($bodyH * 0.7));
        $p->point($tailTipX, $cy + (int) ($bodyH * 0.7));
        $p->background($finColor);
    });

    // Dorsal fin
    $img->drawPolygon(function ($p) use ($cx, $cy, $bodyW, $bodyH, $finColor) {
        $p->point($cx - (int) ($bodyW * 0.1), $cy - (int) ($bodyH / 2) + 2);
        $p->point($cx + (int) ($bodyW * 0.15), $cy - (int) ($bodyH * 0.95));
        $p->point($cx + (int) ($bodyW * 0.25), $cy - (int) ($bodyH / 2) + 2);
        $p->background($finColor);
    });

    // Eye
    $eyeX = $cx + (int) ($bodyW * 0.28);
    $eyeY = $cy - (int) ($bodyH * 0.12);
    $img->drawCircle(function ($c) use ($eyeX, $eyeY) {
        $c->at($eyeX, $eyeY);
        $c->radius(8);
        $c->background('#FFFFFF');
    });
    $img->drawCircle(function ($c) use ($eyeX, $eyeY, $eyeColor) {
        $c->at($eyeX + 2, $eyeY);
        $c->radius(4);
        $c->background($eyeColor);
    });
}

$updated = 0;

foreach ($catalog as $i => $row) {
    $slug = Str::slug($row['name_en']);
    $colors = $categoryColors[$row['category']] ?? ['#04293A', '#064663', '#ECB365'];
    [$bg, $mid, $accent] = $colors;

    $w = 900;
    $h = 675;
    $img = $manager->createImage($w, $h)->fill($bg);

    // Bottom wave band
    $img->drawRectangle(function ($r) use ($w, $h, $mid) {
        $r->size($w, (int) ($h * 0.42));
        $r->at(0, (int) ($h * 0.58));
        $r->background($mid);
    });

    // Bubbles
    mt_srand(1000 + $i);
    for ($b = 0; $b < 10; $b++) {
        $bx = mt_rand(20, $w - 20);
        $by = mt_rand(20, $h - 20);
        $br = mt_rand(3, 14);
        $img->drawCircle(function ($c) use ($bx, $by, $br, $accent) {
            $c->at($bx, $by);
            $c->radius($br);
            $c->border($accent, 1);
        });
    }

    // Fish silhouette — size varies slightly per product
    $bodyW = 280 + ($i % 5) * 18;
    $bodyH = 110 + ($i % 4) * 12;
    $cx = (int) ($w * 0.52) + (($i % 3) - 1) * 40;
    $cy = (int) ($h * 0.42);
    drawFishShape($img, $cx, $cy, $bodyW, $bodyH, $accent, $mid, $bg);

    // Category label bar
    $img->drawRectangle(function ($r) use ($w, $accent) {
        $r->size($w, 48);
        $r->at(0, 0);
        $r->background($accent);
    });

    $img->text(strtoupper($row['category']), 24, 16, function ($font) use ($bg) {
        $font->size(4);
        $font->color($bg);
        $font->align('left', 'top');
    });

    // Product name (English — GD built-in font can't do Bengali reliably)
    $name = $row['name_en'];
    if (strlen($name) > 28) {
        $name = substr($name, 0, 27).'…';
    }
    $img->text($name, (int) ($w / 2), $h - 70, function ($font) {
        $font->size(5);
        $font->color('#FFFFFF');
        $font->align('center', 'center');
    });

    $img->text($row['scientific_name'], (int) ($w / 2), $h - 36, function ($font) use ($accent) {
        $font->size(3);
        $font->color($accent);
        $font->align('center', 'center');
    });

    $relative = "images/products/{$slug}.jpg";
    $absolute = public_path($relative);
    $img->save($absolute, quality: 85);

    $webPath = '/'.$relative;
    Product::where('slug', $slug)->update(['image_path' => $webPath]);
    $updated++;
    echo "OK  {$slug} → {$webPath}\n";
}

echo "\nDone. Updated {$updated} products.\n";
