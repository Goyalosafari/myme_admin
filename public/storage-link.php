<?php
// One-time script — DELETE this file immediately after running!
$target = realpath(__DIR__ . '/../storage/app/public');
$link   = __DIR__ . '/storage';

if (is_link($link)) {
    echo '✅ Symlink already exists: ' . $link . ' → ' . readlink($link);
} elseif (file_exists($link)) {
    echo '❌ A real directory called "storage" already exists inside public/. Remove it first.';
} else {
    if (symlink($target, $link)) {
        echo '✅ Symlink created: ' . $link . ' → ' . $target;
    } else {
        // Fallback: copy files if symlink not allowed (some shared hosts)
        echo '⚠️ symlink() failed. Trying to create folder and copy files...<br>';
        mkdir($link, 0755, true);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $dest = $link . '/' . $files->getSubPathname();
            if ($file->isDir()) {
                mkdir($dest, 0755, true);
            } else {
                copy($file->getRealPath(), $dest);
            }
        }
        echo '✅ Files copied from storage/app/public to public/storage/';
    }
}
