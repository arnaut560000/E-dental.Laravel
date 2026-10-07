<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'dental:create-admin';

    protected $description = 'Create a dental clinic administrator';

    public function handle(): int
    {
        $username = strtolower(trim((string) $this->ask('Username')));
        $name = trim((string) $this->ask('Full name'));
        $password = $this->secret('Password: 12-72 ASCII characters');
        $confirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'username' => $username,
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'username' => ['required', 'string', 'max:50', 'regex:/\A[a-z0-9_.-]+\z/', 'unique:users,username'],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'ascii', 'min:12', 'max:72', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = new User;
        $user->name = $name;
        $user->username = $username;
        $user->email = null;
        $user->password = $password;
        $user->role = 'admin';
        $user->is_active = true;
        $user->must_change_password = false;
        $user->temporary_password_expires_at = null;
        $user->save();

        $this->info('Administrator created successfully.');

        return self::SUCCESS;
    }
}
