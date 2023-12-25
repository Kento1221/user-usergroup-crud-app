<?php

require_once 'vendor/autoload.php';

use Kento1221\UserUsergroupCrudApp\Facades\Database;

$db = Database::getConnection();
$migrationsPath = __DIR__ . '/seeders/';

$files = [];
foreach (new DirectoryIterator($migrationsPath) as $file) {

    if (!$file->isDot() && $file->getExtension() === 'sql') {
        $files[] = $file->getFilename();
    }

}

asort($files);
foreach ($files as $fileName) {

    $commands = file_get_contents($migrationsPath . $fileName);

    if ($db->exec($commands) !== false) {
        echo "Seeding of `" . $fileName . "` run successfully.\n";
    } else {
        echo "An error has occurred while running seeder `" . $fileName . "`: " . $db->errorCode() . "\n";
    }
}

unset($db);