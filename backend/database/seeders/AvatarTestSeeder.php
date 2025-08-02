<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AvatarTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create a simple test avatar for the admin user
        $adminUser = User::where('email', 'admin@dashboard.com')->first();

        if ($adminUser) {
            // Create a simple colored square as test avatar
            $this->createTestAvatar($adminUser->id);
        }
    }

    private function createTestAvatar($userId)
    {
        // Create a simple 200x200 colored square as test avatar
        $width = 200;
        $height = 200;

        // Create image
        $image = imagecreate($width, $height);

        // Define colors
        $background = imagecolorallocate($image, 67, 126, 234); // Primary blue
        $text_color = imagecolorallocate($image, 255, 255, 255); // White

        // Fill background
        imagefill($image, 0, 0, $background);

        // Add text (user initials)
        $user = User::find($userId);
        $initials = strtoupper(substr($user->name, 0, 2));

        // Calculate text position to center it
        $font_size = 5;
        $text_width = imagefontwidth($font_size) * strlen($initials);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;

        imagestring($image, $font_size, $x, $y, $initials, $text_color);

        // Save image
        $filename = 'test-avatar-' . $userId . '.png';
        $path = 'avatars/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Create directory if not exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        imagepng($image, $fullPath);
        imagedestroy($image);

        // Update user avatar
        $user->update(['avatar' => $path]);

        echo "Test avatar created for user: {$user->name}\n";
    }
}
