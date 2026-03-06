<?php
$dir = __DIR__ . '/public/services_images/';
$files = glob($dir . '*.jpg');

foreach ($files as $file) {
    if (!file_exists($file)) continue;

    $originalSize = filesize($file);
    echo "Processing: " . basename($file) . " (Size: " . round($originalSize / 1024 / 1024, 2) . " MB)\n";

    if ($originalSize < 1000000) {
        echo " -> Skipping, already small enough.\n";
        continue;
    }
    
    $img = @imagecreatefromjpeg($file);
    if (!$img) {
        echo " -> Failed to load JPEG.\n";
        continue;
    }

    $w = imagesx($img);
    $h = imagesy($img);

    $new_w = 800; // reasonable width for cards
    $new_h = floor($h * ($new_w / $w));

    $tmp = imagecreatetruecolor($new_w, $new_h);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

    // Save with 80% quality
    imagejpeg($tmp, $file, 80);

    imagedestroy($img);
    imagedestroy($tmp);

    $newSize = filesize($file);
    echo " -> Resized. New size: " . round($newSize / 1024, 2) . " KB\n";
}
echo "Done.\n";
