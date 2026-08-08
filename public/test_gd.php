<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';
use Intervention\Image\ImageManagerStatic as Image;

header('Content-Type: text/html; charset=utf-8');

echo "<h1>GD Text Rendering Test</h1>";
echo "<pre>";

$bg = __DIR__.'/qr-image-v10.jpg';
$font = __DIR__.'/font/DroidArabicKufiRegular.ttf';

if (!file_exists($bg)) {
    die("Background image not found: $bg");
}
if (!file_exists($font)) {
    die("Font not found: $font");
}

echo "Paths are correct.<br>";

$tests = [];
$text = "حفل زفاف عائلة البلاعي";
$Arabic = new \ArPHP\I18N\Arabic('Glyphs');

try {
    // 1. Normal Text
    $img1 = Image::make($bg);
    $img1->text($text, 400, 500, function($f) use ($font) {
        $f->file($font);
        $f->size(90);
        $f->color('#000');
    });
    $img1->save(__DIR__.'/test_1.jpg');
    echo "Test 1 (Normal Text) saved as <a href='test_1.jpg' target='_blank'>test_1.jpg</a><br>";
    $tests[] = 'test_1.jpg';
} catch (\Exception $e) {
    echo "Test 1 Failed: " . $e->getMessage() . "<br>";
}

try {
    // 2. utf8Glyphs
    $text2 = $Arabic->utf8Glyphs($text);
    $img2 = Image::make($bg);
    $img2->text($text2, 400, 500, function($f) use ($font) {
        $f->file($font);
        $f->size(90);
        $f->color('#000');
    });
    $img2->save(__DIR__.'/test_2.jpg');
    echo "Test 2 (utf8Glyphs) saved as <a href='test_2.jpg' target='_blank'>test_2.jpg</a><br>";
    $tests[] = 'test_2.jpg';
} catch (\Exception $e) {
    echo "Test 2 Failed: " . $e->getMessage() . "<br>";
}

try {
    // 3. Reversed Words + utf8Glyphs
    $words = explode(' ', trim($text));
    $reversed_name = implode(' ', array_reverse($words));
    $text3 = $Arabic->utf8Glyphs($reversed_name);
    
    $img3 = Image::make($bg);
    $img3->text($text3, 400, 500, function($f) use ($font) {
        $f->file($font);
        $f->size(90);
        $f->color('#000');
    });
    $img3->save(__DIR__.'/test_3.jpg');
    echo "Test 3 (Reversed + utf8Glyphs) saved as <a href='test_3.jpg' target='_blank'>test_3.jpg</a><br>";
    $tests[] = 'test_3.jpg';
} catch (\Exception $e) {
    echo "Test 3 Failed: " . $e->getMessage() . "<br>";
}

try {
    // 4. Reversed Chars Only (no utf8Glyphs)
    $text4 = implode('', array_reverse(mb_str_split($text, 1, 'UTF-8')));
    $img4 = Image::make($bg);
    $img4->text($text4, 400, 500, function($f) use ($font) {
        $f->file($font);
        $f->size(90);
        $f->color('#000');
    });
    $img4->save(__DIR__.'/test_4.jpg');
    echo "Test 4 (Reversed Chars Only) saved as <a href='test_4.jpg' target='_blank'>test_4.jpg</a><br>";
    $tests[] = 'test_4.jpg';
} catch (\Exception $e) {
    echo "Test 4 Failed: " . $e->getMessage() . "<br>";
}

echo "</pre>";

foreach ($tests as $t) {
    echo "<div style='float:left; margin:10px; border:1px solid #ccc; text-align:center;'>";
    echo "<h3>$t</h3>";
    echo "<img src='$t' width='300'>";
    echo "</div>";
}
