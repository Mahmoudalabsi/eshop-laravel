<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@elegance.com';
$password = 'admin123';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: $email\n";
} else {
    echo "User found: {$user->email}\n";
    if (Hash::check($password, $user->password)) {
        echo "Password matches!\n";
    } else {
        echo "Password DOES NOT match!\n";
        echo "Hash in DB: {$user->password}\n";
    }
}
