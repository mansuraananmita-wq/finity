<?php

/**
 * Map docx embedded images to fish rows by reading table cell order.
 */

$docx = 'C:/Users/MITA/Downloads/fish_store_product_catalog.docx';
$z = new ZipArchive();
$z->open($docx);

$relsXml = $z->getFromName('word/_rels/document.xml.rels');
$docXml = $z->getFromName('word/document.xml');

// rId -> media path
$rels = [];
$rx = new SimpleXMLElement($relsXml);
foreach ($rx->Relationship as $rel) {
    $attrs = $rel->attributes();
    $id = (string) $attrs['Id'];
    $target = (string) $attrs['Target'];
    if (str_contains($target, 'media/')) {
        $rels[$id] = 'word/'.ltrim($target, '/');
    }
}

// Register namespaces
$doc = new SimpleXMLElement($docXml);
$doc->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
$doc->registerXPathNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
$doc->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
$doc->registerXPathNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');

$rows = $doc->xpath('//w:tbl/w:tr');
echo 'Table rows: '.count($rows).PHP_EOL;

$mapped = [];
foreach ($rows as $i => $row) {
    $cells = $row->xpath('./w:tc');
    $texts = [];
    foreach ($cells as $cell) {
        $parts = $cell->xpath('.//w:t');
        $t = '';
        if ($parts) {
            foreach ($parts as $p) {
                $t .= (string) $p;
            }
        }
        $texts[] = trim(preg_replace('/\s+/', ' ', $t));
    }

    // Find image rIds in this row
    $blips = $row->xpath('.//a:blip');
    $imageRids = [];
    if ($blips) {
        foreach ($blips as $blip) {
            $attrs = $blip->attributes('r', true);
            if (isset($attrs['embed'])) {
                $imageRids[] = (string) $attrs['embed'];
            }
        }
    }

    if ($i === 0) {
        echo 'Header: '.json_encode($texts).PHP_EOL;
        continue;
    }

    $name = $texts[1] ?? '';
    $rid = $imageRids[0] ?? null;
    $media = $rid && isset($rels[$rid]) ? $rels[$rid] : null;
    $mapped[] = [
        'row' => $i,
        'category' => $texts[0] ?? '',
        'name' => $name,
        'scientific' => $texts[2] ?? '',
        'price' => $texts[3] ?? '',
        'rid' => $rid,
        'media' => $media,
    ];
    echo sprintf("%02d | %-32s | %s\n", $i, $name, $media ?? 'NO IMAGE');
}

file_put_contents('e:/projects/Finity/storage/app/docx_image_map.json', json_encode($mapped, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nMapped: ".count($mapped)."\n";
$z->close();
