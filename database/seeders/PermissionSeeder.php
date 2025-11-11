<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PermissionEnum::cases() as $permission) {
            Permission::create(['name' => $permission->value]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $editorRole = Role::findByName(RoleEnum::EDITOR->value);
        $editorRole->givePermissionTo([
            PermissionEnum::VIEW_POSTS->value,
            PermissionEnum::CREATE_POSTS->value,
            PermissionEnum::EDIT_POSTS->value,
            PermissionEnum::DELETE_POSTS->value,
            PermissionEnum::VIEW_COMMENTS->value,
            PermissionEnum::CREATE_COMMENTS->value,
            PermissionEnum::EDIT_COMMENTS->value,
            PermissionEnum::DELETE_COMMENTS->value,
        ]);

        $viewerRole = Role::findByName(RoleEnum::VIEWER->value);
        $viewerRole->givePermissionTo(
            PermissionEnum::VIEW_POSTS->value,
            PermissionEnum::VIEW_COMMENTS->value,
        );
    }
}
