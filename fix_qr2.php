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

            // Some were already replaced, some failed. Let us just do string replacements safely with single quotes.
            $second_part = str_replace(
                '$y_datetime     = 1230;', 
                '$y_datetime     = 1300;', 
                $second_part
            );
            $second_part = str_replace(
                '$title_text = $event->title;', 
                '$title_text = $event->name;', 
                $second_part
            );

            // Also make sure to handle EventChatController which had slightly different spacing
            $second_part = str_replace(
                '$y_datetime     = 1230; ', 
                '$y_datetime     = 1300; ', 
                $second_part
            );

            $new_content = $first_part . "qr-image-v10.jpg" . $second_part;
            if ($new_content !== $content) {
                file_put_contents($path, $new_content);
                echo "Updated: $path\n";
            }
        }
    }
}
?>
