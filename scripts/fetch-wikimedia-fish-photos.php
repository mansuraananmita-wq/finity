<?php

/**
 * Download fish photos from Wikipedia species pages (more accurate than Commons search).
 * Run: php scripts/fetch-wikimedia-fish-photos.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

$catalog = require database_path('data/product_catalog.php');
$outDir = public_path('images/products');
@mkdir($outDir, 0755, true);

// Manual Wikipedia title overrides when scientific / common names differ from page titles
$wikiTitles = [
    'common-goldfish' => ['Goldfish', 'Carassius auratus'],
    'fantail-goldfish' => ['Fantail (goldfish)', 'Goldfish'],
    'oranda-goldfish' => ['Oranda', 'Goldfish'],
    'black-moor-goldfish' => ['Black Moor', 'Telescope eye'],
    'ryukin-goldfish' => ['Ryukin', 'Goldfish'],
    'veiltail-betta' => ['Betta', 'Siamese fighting fish'],
    'halfmoon-betta' => ['Betta', 'Siamese fighting fish'],
    'crowntail-betta' => ['Betta', 'Siamese fighting fish'],
    'plakat-betta' => ['Betta', 'Siamese fighting fish'],
    'guppy-mixed-colors' => ['Guppy', 'Poecilia reticulata'],
    'balloon-molly' => ['Poecilia sphenops', 'Molly (fish)'],
    'platy' => ['Southern platyfish', 'Xiphophorus maculatus'],
    'swordtail' => ['Green swordtail', 'Xiphophorus hellerii'],
    'endlers-guppy' => ["Endler's livebearer", 'Poecilia wingei'],
    'neon-tetra' => ['Neon tetra', 'Paracheirodon innesi'],
    'cardinal-tetra' => ['Cardinal tetra', 'Paracheirodon axelrodi'],
    'black-skirt-tetra' => ['Black tetra', 'Gymnocorymbus ternetzi'],
    'ember-tetra' => ['Ember tetra', 'Hyphessobrycon amandae'],
    'rummy-nose-tetra' => ['Rummy-nose tetra', 'Hemigrammus bleheri'],
    'cherry-barb' => ['Cherry barb', 'Puntius titteya'],
    'tiger-barb' => ['Tiger barb', 'Puntigrus tetrazona'],
    'rosy-barb' => ['Rosy barb', 'Pethia conchonius'],
    'dwarf-gourami' => ['Dwarf gourami', 'Trichogaster lalius'],
    'pearl-gourami' => ['Pearl gourami', 'Trichopodus leerii'],
    'blue-gourami' => ['Three spot gourami', 'Trichopodus trichopterus'],
    'angelfish' => ['Freshwater angelfish', 'Pterophyllum scalare'],
    'discus' => ['Discus (fish)', 'Symphysodon'],
    'oscar-fish' => ['Oscar (fish)', 'Astronotus ocellatus'],
    'flowerhorn' => ['Flowerhorn', 'Flowerhorn cichlid'],
    'convict-cichlid' => ['Convict cichlid', 'Amatitlania nigrofasciata'],
    'electric-yellow-cichlid' => ['Labidochromis caeruleus', 'Electric yellow cichlid'],
    'bristlenose-pleco' => ['Ancistrus', 'Bristlenose catfish'],
    'corydoras-catfish' => ['Corydoras', 'Corydoras aeneus'],
    'red-tail-shark' => ['Red-tailed black shark', 'Epalzeorhynchos bicolor'],
    'bala-shark' => ['Bala shark', 'Balantiocheilos melanopterus'],
    'white-cloud-mountain-minnow' => ['White cloud mountain minnow', 'Tanichthys albonubes'],
    'chili-rasbora' => ['Boraras brigittae', 'Chili rasbora'],
    'silver-arowana' => ['Silver arowana', 'Osteoglossum bicirrhosum'],
    'freshwater-stingray' => ['Potamotrygon', 'Freshwater stingray'],
    'cherry-shrimp' => ['Neocaridina davidi', 'Cherry shrimp'],
];

function httpGet(string $url): ?string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_USERAGENT => 'FinityFishStore/1.0 (local catalog bootstrap; contact: admin@finity.test)',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($code >= 200 && $code < 300 && $body !== false) ? $body : null;
}

function wikipediaThumb(string $title): ?string
{
    $api = 'https://en.wikipedia.org/w/api.php?'.http_build_query([
        'action' => 'query',
        'format' => 'json',
        'titles' => $title,
        'prop' => 'pageimages|pageterms',
        'piprop' => 'thumbnail|original',
        'pithumbsize' => 1200,
        'redirects' => 1,
    ]);

    $json = httpGet($api);
    if (! $json) {
        return null;
    }

    $data = json_decode($json, true);
    $pages = $data['query']['pages'] ?? [];
    foreach ($pages as $page) {
        if (isset($page['missing'])) {
            continue;
        }
        return $page['original']['source']
            ?? $page['thumbnail']['source']
            ?? null;
    }

    return null;
}

function saveJpeg(string $bin, string $path): bool
{
    $src = @imagecreatefromstring($bin);
    if (! $src) {
        return false;
    }

    $w = imagesx($src);
    $h = imagesy($src);
    if ($w < 80 || $h < 80) {
        imagedestroy($src);

        return false;
    }

    $maxW = 1200;
    if ($w > $maxW) {
        $nw = $maxW;
        $nh = (int) round($h * ($maxW / $w));
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }

    $ok = imagejpeg($src, $path, 85);
    imagedestroy($src);

    return $ok;
}

// Restore category-colored illustrations first, then overwrite with real photos when found
passthru('php '.escapeshellarg(__DIR__.'/generate-product-images.php'));

$ok = 0;
$fail = 0;

foreach ($catalog as $row) {
    $slug = Str::slug($row['name_en']);
    $path = "{$outDir}/{$slug}.jpg";
    $titles = $wikiTitles[$slug] ?? [$row['name_en'], $row['scientific_name']];

    echo "{$row['name_en']}: ";
    $url = null;
    foreach ($titles as $title) {
        $url = wikipediaThumb($title);
        if ($url) {
            break;
        }
    }

    if (! $url) {
        echo "no wiki image (kept illustration)\n";
        $fail++;
        continue;
    }

    $bin = httpGet($url);
    if (! $bin || ! saveJpeg($bin, $path)) {
        echo "download/convert failed (kept illustration)\n";
        $fail++;
        continue;
    }

    Product::where('slug', $slug)->update(['image_path' => "/images/products/{$slug}.jpg"]);
    echo "OK\n";
    $ok++;
    usleep(250000);
}

echo "\nReal photos: {$ok}. Illustrations kept: {$fail}.\n";
