<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$count = 0;

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $orig = $content;
    
    // 1. guest-services-menu.blade.php special case (where it's inside an <a> tag with specific classes)
    // We already changed it to <a href="https://emcatechnologies.com" ...>
    $content = preg_replace(
        '/<a href="https:\/\/emcatechnologies.com"[^>]*>\s*EmCa\s*Tech(?:n|on)ologies\s*<\/a>/i',
        '<a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: 800; margin-top: 3px; font-size: 14px; letter-spacing: 1px; text-decoration: none;">EmCa Techonologies</a>',
        $content
    );

    // 2. page-login.blade.php and royal-footer.blade.php special case
    $content = preg_replace(
        '/<a href="https:\/\/emca\.tech\/#"[^>]*>\s*EmCa\s*Tech(?:n|on)ologies\s*<\/a>/i',
        '<a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: 600; text-decoration: none;">EmCa Techonologies</a>',
        $content
    );

    // 3. Catch-all for "Powered By EmCa Technologies" (with optional enclosing span/strong/b/a)
    // Note: We use a regex that matches "Powered By" optionally followed by basic inline tags, then "EmCa Tech(on)ologies"
    $emcaLink = '<a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: bold; text-decoration: none;">EmCa Techonologies</a>';
    
    $content = preg_replace(
        '/Powered\s+[Bb]y\s*(?:<(?:strong|b|span|a)[^>]*>)?\s*EmCa\s*Tech(?:n|on)ologies\s*(?:<\/(?:strong|b|span|a)>)?/i',
        'Powered By ' . $emcaLink,
        $content
    );

    if ($orig !== $content) {
        file_put_contents($path, $content);
        $count++;
        echo "Updated: $path\n";
    }
}

echo "Total files updated: $count\n";
