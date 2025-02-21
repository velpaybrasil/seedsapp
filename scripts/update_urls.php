<?php

function updateUrls($directory) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getRealPath());
            
            // Replace URLs in href and action attributes
            $content = preg_replace('/(href|action)=(["\'])\/gcmanager\//', '$1=$2/', $content);
            
            // Replace URLs in JavaScript
            $content = preg_replace('/([\'"])\/gcmanager\//', '$1/', $content);
            
            file_put_contents($file->getRealPath(), $content);
            echo "Updated: " . $file->getRealPath() . PHP_EOL;
        }
    }
}

$viewsDirectory = __DIR__ . '/../views';
echo "Updating URLs in views directory...\n";
updateUrls($viewsDirectory);
echo "Done!\n";
