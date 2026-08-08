<?php
// Test the font directly without Laravel helpers
$font = __DIR__ . '/public/font/DroidArabicKufiRegular.ttf';
$text_original = "حفل زفاف عائلة البلاعي";

echo "Font exists: " . (file_exists($font) ? 'YES ('.filesize($font).' bytes)' : 'NO') . "\n";

// Test 1: raw Arabic text (no processing)
$bg1 = imagecreatetruecolor(900, 150);
$white = imagecolorallocate($bg1, 255, 255, 255);
$dark = imagecolorallocate($bg1, 20, 20, 20);
imagefilledrectangle($bg1, 0, 0, 900, 150, $white);
$r1 = imagettftext($bg1, 40, 0, 50, 90, $dark, $font, $text_original);
echo "Test1 raw: " . ($r1 !== false ? 'OK' : 'FAIL') . "\n";
imagepng($bg1, __DIR__ . '/public/test1_raw.png');
imagedestroy($bg1);

// Test 2: reversed words
$words = explode(' ', trim($text_original));
$text_reversed = implode(' ', array_reverse($words));
$bg2 = imagecreatetruecolor(900, 150);
imagefilledrectangle($bg2, 0, 0, 900, 150, $white);
$r2 = imagettftext($bg2, 40, 0, 50, 90, $dark, $font, $text_reversed);
echo "Test2 reversed: " . ($r2 !== false ? 'OK' : 'FAIL') . "\n";
imagepng($bg2, __DIR__ . '/public/test2_reversed.png');
imagedestroy($bg2);

// Test 3: utf8Glyphs
require __DIR__ . '/vendor/autoload.php';
$Arabic = new \ArPHP\I18N\Arabic('Glyphs');
$text_glyphs = $Arabic->utf8Glyphs($text_original);
$bg3 = imagecreatetruecolor(900, 150);
imagefilledrectangle($bg3, 0, 0, 900, 150, $white);
$r3 = imagettftext($bg3, 40, 0, 50, 90, $dark, $font, $text_glyphs);
echo "Test3 utf8Glyphs: " . ($r3 !== false ? 'OK' : 'FAIL') . "\n";
imagepng($bg3, __DIR__ . '/public/test3_glyphs.png');
imagedestroy($bg3);

echo "\nCheck images:\n";
echo " - public/test1_raw.png\n";
echo " - public/test2_reversed.png\n";
echo " - public/test3_glyphs.png\n";
