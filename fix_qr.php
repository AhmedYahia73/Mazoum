<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . "/app/Http/Controllers");
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, "/^.+\.php$/i", RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (strpos($content, "function update_qr") !== false && strpos($content, "qr-image-v10.jpg") !== false) {
        
        $parts = explode("qr-image-v10.jpg", $content);
        if (count($parts) == 2) {
            $first_part = $parts[0];
            $second_part = $parts[1];

            $second_part = str_replace("backgroundColor(255, 255, 255)", "backgroundColor(255, 255, 255, 0)", $second_part);
            $second_part = preg_replace("/\\$y_datetime\s*=\s*1230;/", "\$y_datetime     = 1300;", $second_part);
            $second_part = str_replace("isset(\$event->title)", "isset(\$event->name)", $second_part);
            $second_part = str_replace("!empty(\$event->title)", "!empty(\$event->name)", $second_part);
            $second_part = preg_replace("/\\$title_text\s*=\s*\\$event->title;/", "\$title_text = \$event->name;", $second_part);

            $new_content = $first_part . "qr-image-v10.jpg" . $second_part;
            if ($new_content !== $content) {
                file_put_contents($path, $new_content);
                echo "Updated: $path\n";
            } else {
                echo "No changes made: $path\n";
            }
        } else {
            echo "Skipped (multiple or zero qr-image-v10.jpg): $path\n";
        }
    }
}
?>
