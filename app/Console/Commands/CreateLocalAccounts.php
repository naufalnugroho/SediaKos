<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateLocalAccounts extends Command
{
    protected $signature = 'kos:akun-lokal';

    protected $description = 'Buat akun uji admin dan penyewa hanya pada lingkungan lokal';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('Perintah ini hanya tersedia pada APP_ENV=local.');

            return self::FAILURE;
        }
        foreach (['admin' => 'owner.local@sediakos.test', 'penyewa' => 'penyewa.local@sediakos.test'] as $role => $email) {
            if (User::where('email', $email)->exists()) {
                $this->info($email.' sudah ada; password tidak diubah.');

                continue;
            }
            $password = Str::random(18);
            User::create(['name' => $role === 'admin' ? 'Owner Lokal' : 'Penyewa Lokal', 'email' => $email, 'password' => Hash::make($password), 'role' => $role]);
            $this->info($role.' | '.$email.' | '.$password);
        }

        return self::SUCCESS;
    }
}
