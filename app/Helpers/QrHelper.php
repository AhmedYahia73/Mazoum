<?php

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;

if (! function_exists('generate_qr_png')) {

    /**
     * Generate a QR code PNG file using GD (no Imagick required).
     *
     * @param string $data      The data to encode
     * @param string $path      Absolute path to save the PNG file
     * @param int    $size      Pixel size of the output image
     * @param array  $color     RGB array for dark modules e.g. [0, 0, 0]
     */
    function generate_qr_png(string $data, string $path, int $size = 300, array $color = [0, 0, 0], bool $transparent = false): void
    {
        // scale = size / (modules * 1) — approximate, QRCode adjusts internally
        $scale = max(1, intval($size / 50));

        $options = new QROptions;
        $options->outputInterface  = QRGdImagePNG::class;
        $options->outputBase64     = false;
        $options->scale            = $scale;
        $options->imageTransparent = false;
        $options->bgColor          = [255, 255, 255];
        $options->moduleValues     = [
            // dark modules
            \chillerlan\QRCode\Data\QRMatrix::M_DATA_DARK        => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DARK      => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_FINDER_DOT       => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_ALIGNMENT_DARK   => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_TIMING_DARK      => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_FORMAT_DARK      => $color,
            \chillerlan\QRCode\Data\QRMatrix::M_VERSION_DARK     => $color,
        ];

        $qrData = (new QRCode($options))->render($data);
        file_put_contents($path, $qrData);

        // إزالة الخلفية البيضاء وجعلها شفافة
        if ($transparent) {
            $src = imagecreatefrompng($path);
            $w   = imagesx($src);
            $h   = imagesy($src);

            $dst = imagecreatetruecolor($w, $h);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);

            // ملء بشفافية كاملة
            $transparent_color = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent_color);

            // نسخ كل pixel — لو أبيض نخليه شفاف
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $rgb = imagecolorat($src, $x, $y);
                    $r   = ($rgb >> 16) & 0xFF;
                    $g   = ($rgb >> 8)  & 0xFF;
                    $b   = $rgb         & 0xFF;

                    // لو الـ pixel فاتح (خلفية بيضاء) → شفاف
                    if ($r > 200 && $g > 200 && $b > 200) {
                        $c = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                    } else {
                        $c = imagecolorallocatealpha($dst, $r, $g, $b, 0);
                    }
                    imagesetpixel($dst, $x, $y, $c);
                }
            }

            imagepng($dst, $path);
            imagedestroy($src);
            imagedestroy($dst);
        }
    }

}
