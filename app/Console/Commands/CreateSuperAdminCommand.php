<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateSuperAdminCommand extends Command
{
    protected $signature = 'maskanoor:create-super-admin
                            {--email= : Super admin email}
                            {--name= : Display name}
                            {--password= : Password (prefer interactive secret prompt)}';

    /** @var list<string> */
    protected $aliases = ['luxsecure:create-super-admin'];

    protected $description = 'Create the first super_admin account (prompted; strong password required).';

    public function handle(): int
    {
        if (User::role('super_admin')->exists()) {
            $this->error('A super_admin already exists. Refusing to create another via this command.');

            return self::FAILURE;
        }

        $email = $this->option('email') ?: $this->ask('Email');
        $name = $this->option('name') ?: ($this->ask('Name', 'Super Admin') ?: 'Super Admin');
        $password = $this->option('password') ?: $this->secret('Password');
        $confirm = $this->option('password') ?: $this->secret('Confirm password');

        if ($password !== $confirm) {
            $this->error('Password confirmation does not match.');

            return self::FAILURE;
        }

        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', Password::min(12)->mixedCase()->numbers()->symbols()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed via User cast
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
            'is_active' => true,
        ])->save();

        $user->syncRoles(['super_admin']);

        $this->info('Super admin created: '.$user->email);

        return self::SUCCESS;
    }
}
