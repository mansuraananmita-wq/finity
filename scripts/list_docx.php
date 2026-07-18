<?php

$z = new ZipArchive();
$z->open('C:/Users/MITA/Downloads/fish_store_product_catalog.docx');
for ($i = 0; $i < $z->numFiles; $i++) {
    echo $z->getNameIndex($i), PHP_EOL;
}
$z->close();
