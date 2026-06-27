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

if ($user) {
    $user->password = Hash::make($password);
    $user->save();
    echo "Password for $email has been reset to: $password (Single Hash)\n";
    
    // Check if it works now
    if (Hash::check($password, $user->password)) {
        echo "Verification successful: Password matches.\n";
    } else {
        echo "Verification FAILED: Password still does not match. Check if something else is hashing it.\n";
    }
} else {
    echo "User $email not found. Creating a new admin user...\n";
    User::create([
        'name' => 'Admin',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
        'status' => 1
    ]);
    echo "Admin user created with email: $email and password: $password\n";
}
