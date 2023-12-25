<?php

require_once 'vendor/autoload.php';

use Kento1221\UserUsergroupCrudApp\Facades\Database;

$db = Database::getConnection();
$migrationsPath = __DIR__ . '/migrations/';

$files = [];
foreach (new DirectoryIterator($migrationsPath) as $file) {

    if (!$file->isDot() && $file->getExtension() === 'php') {
        $files[] = $file->getFilename();
    }

}

asort($files);
foreach ($files as $fileName) {

    $migrationQuery = require $migrationsPath . $fileName;

    if ($db->query($migrationQuery)) {
        echo "Migration of `" . $fileName . "` run successfully.\n";
    } else {
        echo "An error has occurred while running `" . $fileName . "`: " . $db->errorCode() . "\n";
    }
}

unset($db);