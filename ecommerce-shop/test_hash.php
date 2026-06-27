<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$password = 'admin123';
$hashedByHand = Hash::make($password);

$user = new User();
$user->password = $hashedByHand;

echo "Hashed by hand: $hashedByHand\n";
echo "Property value after set: " . $user->password . "\n";

if ($hashedByHand === $user->password) {
    echo "NO DOUBLE HASHING DETECTED.\n";
} else {
    echo "DOUBLE HASHING DETECTED!\n";
}
