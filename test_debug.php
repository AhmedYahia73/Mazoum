<?php
require 'vendor/autoload.php';

// اختبار كل مسارات الخطوط المحتملة
$font_paths = [
    base_path('resources/fonts/DroidArabicKufiRegular.ttf'),
    base_path('resources/fonts/arabic_font/DroidArabicKufiRegular.ttf'),
    public_path('font/DroidArabicKufiRegular.ttf'),
];

echo "=== Font Check ===\n";
foreach ($font_paths as $p) {
    echo $p . " => " . (file_exists($p) ? "EXISTS (" . filesize($p) . " bytes)" : "NOT FOUND") . "\n";
}

// اختبار الصورة مع الخط الموجود
$Arabic = new \ArPHP\I18N\Arabic('Glyphs');
$text = "حفل زفاف عائلة البلاعي";
$text_glyphs = $Arabic->utf8Glyphs($text);

echo "\n=== Text ===\n";
echo "Original: " . $text . "\n";
echo "After utf8Glyphs length: " . strlen($text_glyphs) . "\n";

// اختبر إنشاء صورة بالخط الموجود
$found_font = null;
foreach ($font_paths as $p) {
    if (file_exists($p)) {
        $found_font = $p;
        break;
    }
}

if ($found_font) {
    echo "\n=== Image Test with font: $found_font ===\n";
    $bg = imagecreatetruecolor(800, 200);
    $white = imagecolorallocate($bg, 255, 255, 255);
    $black = imagecolorallocate($bg, 0, 0, 0);
    imagefilledrectangle($bg, 0, 0, 800, 200, $white);
    
    $bbox = imagettftext($bg, 40, 0, 50, 100, $black, $found_font, $text_glyphs);
    if ($bbox === false) {
        echo "imagettftext FAILED!\n";
    } else {
        echo "imagettftext SUCCESS!\n";
        imagepng($bg, public_path('test_arabic_result.png'));
        echo "Saved to public/test_arabic_result.png\n";
    }
    imagedestroy($bg);
} else {
    echo "\nNO FONT FOUND!\n";
}
