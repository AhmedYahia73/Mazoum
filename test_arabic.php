<?php
require 'vendor/autoload.php';
$bg = imagecreatetruecolor(800, 400);
$white = imagecolorallocate($bg, 255, 255, 255);
$black = imagecolorallocate($bg, 0, 0, 0);
imagefilledrectangle($bg, 0, 0, 800, 400, $white);

$Arabic = new \ArPHP\I18N\Arabic('Glyphs');
$text = "حفل زفاف عائلة البلاعي";
$text = $Arabic->utf8Glyphs($text);

$font1 = __DIR__ . '/public/font/DroidArabicKufiRegular.ttf';
$font2 = __DIR__ . '/public/font/Amiri.ttf';
$font3 = __DIR__ . '/public/font/timr45w.ttf';

if (file_exists($font1)) {
    imagettftext($bg, 30, 0, 50, 100, $black, $font1, "Droid: " . $text);
}
if (file_exists($font2)) {
    imagettftext($bg, 30, 0, 50, 200, $black, $font2, "Amiri: " . $text);
}
if (file_exists($font3)) {
    imagettftext($bg, 30, 0, 50, 300, $black, $font3, "Timr: " . $text);
}

imagepng($bg, 'public/test_arabic_gd.png');
echo "Done! Check public/test_arabic_gd.png";
