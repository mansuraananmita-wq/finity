<?php

$docx = 'C:/Users/MITA/Downloads/fish_store_product_catalog.docx';
$zip = new ZipArchive();
if ($zip->open($docx) !== true) {
    fwrite(STDERR, "Cannot open docx\n");
    exit(1);
}
$xml = $zip->getFromName('word/document.xml');
$zip->close();

$xml = preg_replace('/<w:tab[^\\/]*\\/>/', "\t", $xml);
$text = strip_tags(str_replace(['</w:p>', '</w:tr>', '</w:tc>'], ["\n", "\n", "\t"], $xml));
$text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
$text = preg_replace("/[ \\t]+/", ' ', $text);
$text = preg_replace("/\\n{2,}/", "\n", $text);

file_put_contents('e:/projects/Finity/storage/app/catalog_docx.txt', $text);
echo substr($text, 0, 5000);
