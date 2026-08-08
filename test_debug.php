<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Intervention\Image\ImageManagerStatic as Image;

try {
    $bg = public_path('qr-image-v10.jpg');
    if (!file_exists($bg)) {
        die("Background image not found: $bg");
    }

    $img = Image::make($bg);
    
    $arabic_font = public_path('font/DroidArabicKufiRegular.ttf');
    if (!file_exists($arabic_font)) {
        die("Font not found: $arabic_font");
    }

    $text = "حفل زفاف عائلة البلاعي";

    // Test 1: Normal text
    $img->text($text, 200, 100, function ($font) use ($arabic_font) {
        $font->file($arabic_font);
        $font->size(50);
        $font->color('#000');
    });

    // Test 2: utf8Glyphs
    $Arabic = new \ArPHP\I18N\Arabic('Glyphs');
    $text2 = $Arabic->utf8Glyphs($text);
    $img->text($text2, 200, 200, function ($font) use ($arabic_font) {
        $font->file($arabic_font);
        $font->size(50);
        $font->color('#000');
    });

    // Test 3: Reversed words + utf8Glyphs
    $words = explode(' ', trim($text));
    $words_rev = array_reverse($words);
    $reversed_name = implode(' ', $words_rev);
    $text3 = $Arabic->utf8Glyphs($reversed_name);
    $img->text($text3, 200, 300, function ($font) use ($arabic_font) {
        $font->file($arabic_font);
        $font->size(50);
        $font->color('#000');
    });

    $path = public_path('test_text_render.jpg');
    $img->save($path);
    echo "Saved to: $path";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
