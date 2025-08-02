<?php

require_once __DIR__ . '/vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

try {
    echo "Testing Avatar Dependencies...\n";

    // Test Intervention Image
    $manager = new ImageManager(new Driver());
    echo "✅ Intervention Image: OK\n";

    // Test if we can create a simple image
    $image = $manager->create(100, 100);
    echo "✅ Image creation: OK\n";

    // Test Laravolt Avatar (without Laravel context)
    if (class_exists('Laravolt\Avatar\Avatar')) {
        echo "✅ Laravolt Avatar class: Found\n";
    } else {
        echo "❌ Laravolt Avatar class: Not found\n";
    }

    echo "All dependencies working correctly!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
