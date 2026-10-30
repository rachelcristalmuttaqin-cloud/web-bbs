<?php
// Script yang menggunakan Laravel bootstrap untuk koneksi ke Neon
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Ubah koneksi ke pgsql Neon
config([
    'database.default' => 'pgsql',
    'database.connections.pgsql' => [
        'driver' => 'pgsql',
        'url' => 'postgresql://neondb_owner:npg_NWJ50DEungMY@ep-misty-band-ao2a9zo5-pooler.c-2.ap-southeast-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'require',
    ],
]);

try {
    $users = DB::connection('pgsql')
        ->table('users')
        ->whereRaw("name ILIKE '%rachel%' OR name ILIKE '%cristal%' OR name ILIKE '%muttaqin%'")
        ->select('id', 'name', 'email', 'role', 'status')
        ->get();

    if ($users->isEmpty()) {
        echo "Tidak ada user dengan nama tersebut ditemukan.\n";
    } else {
        echo "=== USER DITEMUKAN ===\n";
        foreach ($users as $u) {
            echo "ID: {$u->id} | Nama: {$u->name} | Email: {$u->email} | Role: {$u->role} | Status: {$u->status}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
