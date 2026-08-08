$files = Get-ChildItem -Path "c:\xampp\htdocs\Mazoum\app\Http\Controllers" -Recurse -Filter "*.php" | Where-Object { (Get-Content $_.FullName -Raw) -match 'function update_qr' }

foreach ($file in $files) {
    $content = Get-Content $_.FullName -Raw -Encoding UTF8

    # Fix 1: replace base_path resources/fonts with public_path + fallback
    $oldFont = "\`$arabic_font = base_path('resources/fonts/DroidArabicKufiRegular.ttf'); "
    $newFont = "`$arabic_font = public_path('font/DroidArabicKufiRegular.ttf');`r`n            if (!file_exists(`$arabic_font)) {`r`n                `$arabic_font = base_path('resources/fonts/DroidArabicKufiRegular.ttf');`r`n            }"
    $content = $content.Replace($oldFont, $newFont)

    # Fix 2: replace old utf8Glyphs block for event->name with word-reversal
    $oldTitle = @"
            // أ- إضافة عنوان المناسبة (Event Title)
            if (isset(`$event->name)) {
                `$title_text = `$event->name;
                
                // [إصلاح]: تمت إزالة التكرار الخاطئ الذي كان يطبق المعالجة العربية على اللغات الأخرى
                if (isset(`$event->language) && `$event->language == 'ar') {
                    `$Arabic = new \ArPHP\I18N\Arabic('Glyphs');
                    `$title_text = `$Arabic->utf8Glyphs(`$title_text);
                }

                `$img->text(`$title_text, `$center_x, `$y_title, function (`$font) use (`$arabic_font) {
                    `$font->file(`$arabic_font);
                    `$font->size(90);
                    `$font->color('#fff'); 
                    `$font->align('center');
                    `$font->valign('middle');
                });
            }
"@

    $newTitle = @"
            // أ- إضافة عنوان المناسبة (Event Title)
            // نعكس ترتيب الكلمات - FreeType يربط الحروف تلقائياً
            if (!empty(trim(`$event->name ?? ''))) {
                `$words = explode(' ', trim(`$event->name));
                `$title_text = implode(' ', array_reverse(`$words));

                `$img->text(`$title_text, `$center_x, `$y_title, function (`$font) use (`$arabic_font) {
                    `$font->file(`$arabic_font);
                    `$font->size(90);
                    `$font->color('#fff'); 
                    `$font->align('center');
                    `$font->valign('middle');
                });
            }
"@

    $content = $content.Replace($oldTitle, $newTitle)

    Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
    Write-Output "Processed: $($file.Name)"
}
