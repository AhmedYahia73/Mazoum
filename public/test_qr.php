<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Intervention\Image\Facades\Image;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;

$bg_file = '6929c7748559e.png';
$bg_path = public_path('images/' . $bg_file);
echo "BG exists: " . (file_exists($bg_path) ? 'YES' : 'NO') . "\n";

$qr_dir = public_path('qr_code');
if (!file_exists($qr_dir)) mkdir($qr_dir, 0777, true);

$qr_png = $qr_dir . '/test_tmp.png';
$final  = $qr_dir . '/test_final.png';

// Generate QR using GD PNG
$options = new QROptions;
$options->outputInterface  = QRGdImagePNG::class;
$options->outputBase64     = false;
$options->scale            = 5;
$options->imageTransparent = false;

$qrData = (new QRCode($options))->render('https://test.com');
file_put_contents($qr_png, $qrData);

echo "QR PNG exists: " . (file_exists($qr_png) ? 'YES (' . filesize($qr_png) . ' bytes)' : 'NO') . "\n";

// Check first bytes
$bytes = array_slice(unpack('C*', file_get_contents($qr_png, false, null, 0, 4)), 0, 4);
echo "Magic bytes: " . implode(' ', array_map(fn($b) => sprintf('%02X', $b), $bytes)) . " (PNG should be 89 50 4E 47)\n";

$bg = Image::make($bg_path);
echo "BG size: " . $bg->width() . "x" . $bg->height() . "\n";

$qr = Image::make($qr_png);
echo "QR size: " . $qr->width() . "x" . $qr->height() . "\n";

// Resize QR
$qr_width = 150; $qr_height = 150;
$qr->resize($qr_width, $qr_height);

// bottom-right origin: qr_x=100, qr_y=100
$qr_x = 100; $qr_y = 100;
$x = $bg->width()  - $qr->width()  - $qr_x;
$y = $bg->height() - $qr->height() - $qr_y;
echo "Position: x=$x, y=$y\n";

$bg->insert($qr, 'top-left', $x, $y);
$bg->save($final, 100);
echo "Final saved: " . (file_exists($final) ? 'YES' : 'NO') . "\n";
echo "Check: http://localhost/Mazoum/qr_code/test_final.png\n";

@unlink($qr_png);
