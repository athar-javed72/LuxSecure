<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('guard_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('remember_token');
        });

        $guard = config('auth.defaults.guard', 'web');
        $roleNames = config('rbac.roles', []);
        $systemRoles = config('rbac.system_roles', $roleNames);

        foreach ($roleNames as $name) {
            Role::findOrCreate($name, $guard);
            Role::findByName($name, $guard)->update([
                'is_system' => in_array($name, $systemRoles, true),
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (Schema::hasColumn('users', 'role')) {
            $adminRole = Role::findByName('admin', $guard);
            $clientRole = Role::findByName('client', $guard);
            $modelType = config('auth.providers.users.model', \App\Models\User::class);

            $users = DB::table('users')->select('id', 'role')->get();
            foreach ($users as $user) {
                $roleId = ($user->role === 'admin') ? $adminRole->id : $clientRole->id;
                $exists = DB::table('model_has_roles')
                    ->where('role_id', $roleId)
                    ->where('model_type', $modelType)
                    ->where('model_id', $user->id)
                    ->exists();

                if (! $exists) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleId,
                        'model_type' => $modelType,
                        'model_id' => $user->id,
                    ]);
                }
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('user')->after('password');
            });
        }

        $modelType = config('auth.providers.users.model', \App\Models\User::class);
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $users = DB::table('users')->pluck('id');
        foreach ($users as $userId) {
            $isAdmin = $adminRoleId && DB::table('model_has_roles')
                ->where('model_id', $userId)
                ->where('model_type', $modelType)
                ->where('role_id', $adminRoleId)
                ->exists();
            DB::table('users')->where('id', $userId)->update([
                'role' => $isAdmin ? 'admin' : 'user',
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        if (Schema::hasColumn('roles', 'is_system')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('is_system');
            });
        }
    }
};
