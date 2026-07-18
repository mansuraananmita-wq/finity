<?php
$xml = file_get_contents(__DIR__ . '/catalog_extract/word/document.xml');
$xml = preg_replace('/<w:tab[^>]*\/>/', '|', $xml);
$text = strip_tags(str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xml));
$text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
file_put_contents(__DIR__ . '/catalog_text.txt', $text);
echo $text;
