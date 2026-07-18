<?php

/**
 * Extract docx product photos in document order and assign to fish by row order.
 * Then save as /images/products/{slug}.jpg and update DB.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

$docx = 'C:/Users/MITA/Downloads/fish_store_product_catalog.docx';
$z = new ZipArchive();
if ($z->open($docx) !== true) {
    fwrite(STDERR, "Cannot open docx\n");
    exit(1);
}

$relsXml = $z->getFromName('word/_rels/document.xml.rels');
$docXml = $z->getFromName('word/document.xml');

// rId -> media path
preg_match_all(
    '/Id="(rId\d+)"[^>]*Target="(media\/[^"]+)"|Target="(media\/[^"]+)"[^>]*Id="(rId\d+)"/',
    $relsXml,
    $m,
    PREG_SET_ORDER
);
$rels = [];
foreach ($m as $match) {
    if (! empty($match[1]) && ! empty($match[2])) {
        $rels[$match[1]] = 'word/'.$match[2];
    } elseif (! empty($match[4]) && ! empty($match[3])) {
        $rels[$match[4]] = 'word/'.$match[3];
    }
}
// More reliable parse
$rels = [];
if (preg_match_all('/<Relationship[^>]+>/', $relsXml, $tags)) {
    foreach ($tags[0] as $tag) {
        if (! preg_match('/Id="([^"]+)"/', $tag, $id)) {
            continue;
        }
        if (! preg_match('/Target="(media\/[^"]+)"/', $tag, $tg)) {
            continue;
        }
        $rels[$id[1]] = 'word/'.$tg[1];
    }
}

// Embed order in document
preg_match_all('/r:embed="(rId\d+)"/', $docXml, $embeds);
$embedOrder = $embeds[1] ?? [];

echo 'Rels with media: '.count($rels).PHP_EOL;
echo 'Embeds in doc: '.count($embedOrder).PHP_EOL;

$catalog = require database_path('data/product_catalog.php');
if (count($catalog) !== count($embedOrder)) {
    echo 'WARNING: catalog '.count($catalog).' vs embeds '.count($embedOrder).PHP_EOL;
}

$outDir = public_path('images/products');
@mkdir($outDir, 0755, true);

$n = min(count($catalog), count($embedOrder));
$ok = 0;

for ($i = 0; $i < $n; $i++) {
    $row = $catalog[$i];
    $rid = $embedOrder[$i];
    $media = $rels[$rid] ?? null;
    $slug = Str::slug($row['name_en']);
    $dest = "{$outDir}/{$slug}.jpg";

    echo sprintf('%02d %-32s %s -> ', $i + 1, $row['name_en'], $media ?? 'MISSING');

    if (! $media) {
        echo "no media\n";
        continue;
    }

    $bin = $z->getFromName($media);
    if ($bin === false) {
        echo "read fail\n";
        continue;
    }

    $src = @imagecreatefromstring($bin);
    if (! $src) {
        // keep original png if jpeg convert fails
        $destPng = "{$outDir}/{$slug}.png";
        file_put_contents($destPng, $bin);
        Product::where('slug', $slug)->update(['image_path' => "/images/products/{$slug}.png"]);
        echo "saved png\n";
        $ok++;
        continue;
    }

    $w = imagesx($src);
    $h = imagesy($src);
    $maxW = 1200;
    if ($w > $maxW) {
        $nw = $maxW;
        $nh = (int) round($h * ($maxW / $w));
        $dst = imagecreatetruecolor($nw, $nh);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);
        $src = $dst;
    } else {
        // Flatten transparency onto white
        $dst = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }

    imagejpeg($src, $dest, 88);
    imagedestroy($src);

    Product::where('slug', $slug)->update(['image_path' => "/images/products/{$slug}.jpg"]);
    echo "OK\n";
    $ok++;
}

$z->close();
echo "\nImported {$ok} photos from docx.\n";
