<?php

namespace App\Services\WhatsApp;

use App\Models\Events;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class WhatsAppMediaOptimizerService
{
    /**
     * القيمة الافتراضية لعرض الصورة المحسنة لقوالب واتساب
     */
    public const DEFAULT_MAX_WIDTH = 1080;

    /**
     * القيمة الافتراضية لارتفاع الصورة المحسنة لقوالب واتساب
     */
    public const DEFAULT_MAX_HEIGHT = 1920;

    /**
     * جودة صورة الـ JPEG المحسنة (من 0 إلى 100)
     */
    public const DEFAULT_QUALITY = 85;

    /**
     * الحصول على رابط صورة هيدر مُحسّن ومطابق لمتطلبات ميتا وواتساب لحدث معين
     *
     * @param Events|mixed $event
     * @param int $maxWidth
     * @param int $maxHeight
     * @return string
     */
    public static function getOptimizedEventHeaderImage($event, int $maxWidth = self::DEFAULT_MAX_WIDTH, int $maxHeight = self::DEFAULT_MAX_HEIGHT): string
    {
        if (!$event) {
            return self::getFallbackImageUrl();
        }

        // اختيار أفضل ملف صورة للحدث (الأولوية لكرت الدعوة file ثم image ثم pdf)
        $chosenRaw = self::selectEventImageSource($event);

        if (empty($chosenRaw)) {
            return self::getFallbackImageUrl();
        }

        // محاولة إيجاد مسار الملف الفعلي على السيرفر
        $localPath = self::resolveLocalFilePath($chosenRaw);

        if ($localPath && file_exists($localPath)) {
            $optimizedUrl = self::optimizeLocalImageFile($localPath, basename($chosenRaw), $maxWidth, $maxHeight);
            if ($optimizedUrl) {
                return $optimizedUrl;
            }
        }

        // في حال لم يكن الملف محلياً، نستخدم الرابط الأصلي بعد تصحيح الدومين
        $url = is_string($chosenRaw) && preg_match('#^https?://#i', $chosenRaw)
            ? $chosenRaw
            : asset('images/' . ltrim($chosenRaw, '/'));

        return self::normalizePublicUrl($url);
    }

    /**
     * تحسين أي رابط صورة هيدر لواتساب سواء كان قادماً من حدث أو من طلب مخصص
     *
     * @param string|null $imageUrl
     * @param int $maxWidth
     * @param int $maxHeight
     * @return string
     */
    public static function optimizeHeaderImageUrl(?string $imageUrl, int $maxWidth = self::DEFAULT_MAX_WIDTH, int $maxHeight = self::DEFAULT_MAX_HEIGHT): string
    {
        if (empty($imageUrl)) {
            return self::getFallbackImageUrl();
        }

        // إذا كان الرابط محسناً بالفعل
        if (str_contains($imageUrl, '/images/optimized/')) {
            return self::normalizePublicUrl($imageUrl);
        }

        // استخراج اسم الملف من الرابط
        $parsedPath = parse_url($imageUrl, PHP_URL_PATH);
        $filename   = $parsedPath ? basename($parsedPath) : basename($imageUrl);

        // إذا كان الملف PDF، لا يمكن استخدامه كهيدر صورة في ميتا
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf') {
            Log::warning("WhatsAppMediaOptimizer: تم تمرير ملف PDF كهيدر صورة، سيتم استخدام الصورة الاحتياطية: {$filename}");
            return self::getFallbackImageUrl();
        }

        $localPath = self::resolveLocalFilePath($filename);

        if ($localPath && file_exists($localPath)) {
            $optimized = self::optimizeLocalImageFile($localPath, $filename, $maxWidth, $maxHeight);
            if ($optimized) {
                return $optimized;
            }
        }

        return self::normalizePublicUrl($imageUrl);
    }

    /**
     * معالجة وضغط الصورة محلياً باستخدام GD وتخزينها في المجلد المؤقت المُحسّن
     *
     * @param string $sourcePath المسار الكامل للملف على القرص
     * @param string $baseName اسم الملف
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string|null الرابط الكامل للملف المحسّن
     */
    public static function optimizeLocalImageFile(
        string $sourcePath,
        string $baseName,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $maxHeight = self::DEFAULT_MAX_HEIGHT,
        int $quality = self::DEFAULT_QUALITY
    ): ?string {
        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));

        // ملفات الـ PDF لا تعالج عبر GD
        if ($ext === 'pdf') {
            return null;
        }

        $optimizedDir = public_path('images/optimized');
        if (!is_dir($optimizedDir)) {
            @mkdir($optimizedDir, 0777, true);
        }

        // اسم الملف المحسّن يعتمد على الاسم الأصلي والأبعاد لضمان الكاش
        $cleanBaseName = pathinfo($baseName, PATHINFO_FILENAME);
        $targetFilename = "opt_{$maxWidth}_{$cleanBaseName}.jpg";
        $targetPath = $optimizedDir . DIRECTORY_SEPARATOR . $targetFilename;

        // إذا كانت الصورة المحسنة موجودة مسبقاً، نرجع رابطها مباشرة بدون إعادة معالجة
        if (file_exists($targetPath) && filesize($targetPath) > 0) {
            return self::normalizePublicUrl(asset("images/optimized/{$targetFilename}"));
        }

        // رفع حد الذاكرة مؤقتاً لمعالجة الصور الضخمة (مثل 3375x6000 px)
        $previousMemoryLimit = ini_get('memory_limit');
        if (self::parseMemoryLimit($previousMemoryLimit) < 536870912) { // 512MB
            @ini_set('memory_limit', '512M');
        }

        try {
            $imageInfo = @getimagesize($sourcePath);
            if (!$imageInfo) {
                return null;
            }

            $srcWidth  = $imageInfo[0];
            $srcHeight = $imageInfo[1];
            $imageType = $imageInfo[2];

            // تحميل الصورة حسب نوعها
            $srcImage = match ($imageType) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG  => @imagecreatefrompng($sourcePath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
                IMAGETYPE_GIF  => @imagecreatefromgif($sourcePath),
                default        => null,
            };

            if (!$srcImage) {
                Log::warning("WhatsAppMediaOptimizer: تعذر قراءة الصورة عبر GD: {$sourcePath}");
                return null;
            }

            // حساب الأبعاد المحسوبة بنسب متناسبة بدون تشويه
            $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1.0);
            $targetWidth  = (int) round($srcWidth * $scale);
            $targetHeight = (int) round($srcHeight * $scale);

            // إنشاء الكانفاس الجديد
            $targetCanvas = imagecreatetruecolor($targetWidth, $targetHeight);

            // تعبئة الخلفية باللون الأبيض الممتلئ لتجنب مشكلة الشفافية التي تظهر بلون فقاعة واتساب الخضراء
            $white = imagecolorallocate($targetCanvas, 255, 255, 255);
            imagefill($targetCanvas, 0, 0, $white);

            // نسخ وتنعيم الصورة بدقة عالية
            imagecopyresampled(
                $targetCanvas,
                $srcImage,
                0, 0, 0, 0,
                $targetWidth,
                $targetHeight,
                $srcWidth,
                $srcHeight
            );

            // حفظ الصورة كـ JPEG محسّن عالي الجودة
            imagejpeg($targetCanvas, $targetPath, $quality);

            // تحرير الذاكرة فوراً
            imagedestroy($srcImage);
            imagedestroy($targetCanvas);

            Log::info("WhatsAppMediaOptimizer: تم تحسين صورة الهيدر بنجاح [{$srcWidth}x{$srcHeight} -> {$targetWidth}x{$targetHeight}] وحفظها في: {$targetFilename}");

            return self::normalizePublicUrl(asset("images/optimized/{$targetFilename}"));

        } catch (\Throwable $e) {
            Log::error("WhatsAppMediaOptimizer: خطأ أثناء تحسين الصورة: {$e->getMessage()}", [
                'source' => $sourcePath,
                'trace'  => $e->getTraceAsString(),
            ]);
            return null;
        } finally {
            if ($previousMemoryLimit) {
                @ini_set('memory_limit', $previousMemoryLimit);
            }
        }
    }

    /**
     * اختيار ملف الصورة المناسب من الحدث مع استبعاد الـ PDF
     *
     * @param Events|mixed $event
     * @return string|null
     */
    protected static function selectEventImageSource($event): ?string
    {
        $getVal = function ($key) use ($event) {
            if (is_array($event)) {
                return $event[$key] ?? null;
            }
            if (is_object($event)) {
                if (method_exists($event, 'getAttributes')) {
                    $attrs = $event->getAttributes();
                    if (!empty($attrs[$key])) {
                        return $attrs[$key];
                    }
                }
                if (method_exists($event, 'getRawOriginal')) {
                    $raw = $event->getRawOriginal($key);
                    if (!empty($raw)) {
                        return $raw;
                    }
                }
                if (isset($event->{$key}) && !empty($event->{$key})) {
                    return $event->{$key};
                }
            }
            return null;
        };

        $rawFile  = $getVal('file');
        $rawImage = $getVal('image');
        $rawPdf   = $getVal('pdf');

        // 1. كرت الدعوة الرئيسي
        if (!empty($rawFile) && !self::isPdf($rawFile)) {
            return $rawFile;
        }

        // 2. كرت الخلفية / الدخول
        if (!empty($rawImage) && !self::isPdf($rawImage)) {
            return $rawImage;
        }

        // 3. في بعض الأحيان يخزن المستخدم صورة في حقل pdf
        if (!empty($rawPdf) && !self::isPdf($rawPdf)) {
            return $rawPdf;
        }

        return $rawFile ?: $rawImage;
    }

    /**
     * فحص ما إذا كان الامتداد PDF
     */
    protected static function isPdf(?string $filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $ext = strtolower(pathinfo(parse_url($filename, PHP_URL_PATH) ?? $filename, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }

    /**
     * محاولة الوصول لمسار الملف الفعلي داخل مجلدات المشروع
     */
    protected static function resolveLocalFilePath(string $filenameOrUrl): ?string
    {
        $clean = parse_url($filenameOrUrl, PHP_URL_PATH);
        $cleanName = $clean ? basename($clean) : basename($filenameOrUrl);

        $candidates = [
            public_path('images/' . $cleanName),
            public_path('public/images/' . $cleanName),
            base_path('public/images/' . $cleanName),
            public_path($cleanName),
            base_path($cleanName),
            storage_path('app/public/images/' . $cleanName),
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * تصحيح الرابط ليضمن العمل في بيئة الـ Cron Job وميتا (بدون localhost)
     */
    public static function normalizePublicUrl(string $url): string
    {
        // استبدال localhost أو 127.0.0.1 بالدومين الرسمي
        $url = preg_replace('#^https?://(localhost|127\.0\.0\.1)(:\d+)?#i', 'https://mazoom.online', $url);

        // إجبار HTTPS
        if (str_starts_with($url, 'http://') && !str_contains($url, 'localhost')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    /**
     * رابط الصورة الاحتياطية الرسمية
     */
    public static function getFallbackImageUrl(): string
    {
        return self::normalizePublicUrl(asset('11.png'));
    }

    /**
     * تحويل صيغة memory_limit مثل 128M أو 1G إلى بايت
     */
    protected static function parseMemoryLimit(string $val): int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $num = (int) $val;
        return match ($last) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => $num,
        };
    }
}
