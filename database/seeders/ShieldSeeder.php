<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_bidding","view_any_bidding","create_bidding","update_bidding","restore_bidding","restore_any_bidding","replicate_bidding","reorder_bidding","delete_bidding","delete_any_bidding","force_delete_bidding","force_delete_any_bidding","view_bidding::adjudication::type","view_any_bidding::adjudication::type","create_bidding::adjudication::type","update_bidding::adjudication::type","restore_bidding::adjudication::type","restore_any_bidding::adjudication::type","replicate_bidding::adjudication::type","reorder_bidding::adjudication::type","delete_bidding::adjudication::type","delete_any_bidding::adjudication::type","force_delete_bidding::adjudication::type","force_delete_any_bidding::adjudication::type","view_bidding::data::source","view_any_bidding::data::source","create_bidding::data::source","update_bidding::data::source","restore_bidding::data::source","restore_any_bidding::data::source","replicate_bidding::data::source","reorder_bidding::data::source","delete_bidding::data::source","delete_any_bidding::data::source","force_delete_bidding::data::source","force_delete_any_bidding::data::source","view_bidding::state","view_any_bidding::state","create_bidding::state","update_bidding::state","restore_bidding::state","restore_any_bidding::state","replicate_bidding::state","reorder_bidding::state","delete_bidding::state","delete_any_bidding::state","force_delete_bidding::state","force_delete_any_bidding::state","view_bidding::type","view_any_bidding::type","create_bidding::type","update_bidding::type","restore_bidding::type","restore_any_bidding::type","replicate_bidding::type","reorder_bidding::type","delete_bidding::type","delete_any_bidding::type","force_delete_bidding::type","force_delete_any_bidding::type","view_call","view_any_call","create_call","update_call","restore_call","restore_any_call","replicate_call","reorder_call","delete_call","delete_any_call","force_delete_call","force_delete_any_call","view_city","view_any_city","create_city","update_city","restore_city","restore_any_city","replicate_city","reorder_city","delete_city","delete_any_city","force_delete_city","force_delete_any_city","view_client","view_any_client","create_client","update_client","restore_client","restore_any_client","replicate_client","reorder_client","delete_client","delete_any_client","force_delete_client","force_delete_any_client","view_deadline","view_any_deadline","create_deadline","update_deadline","restore_deadline","restore_any_deadline","replicate_deadline","reorder_deadline","delete_deadline","delete_any_deadline","force_delete_deadline","force_delete_any_deadline","view_estimate","view_any_estimate","create_estimate","update_estimate","restore_estimate","restore_any_estimate","replicate_estimate","reorder_estimate","delete_estimate","delete_any_estimate","force_delete_estimate","force_delete_any_estimate","view_province","view_any_province","create_province","update_province","restore_province","restore_any_province","replicate_province","reorder_province","delete_province","delete_any_province","force_delete_province","force_delete_any_province","view_region","view_any_region","create_region","update_region","restore_region","restore_any_region","replicate_region","reorder_region","delete_region","delete_any_region","force_delete_region","force_delete_any_region","view_service::type","view_any_service::type","create_service::type","update_service::type","restore_service::type","restore_any_service::type","replicate_service::type","reorder_service::type","delete_service::type","delete_any_service::type","force_delete_service::type","force_delete_any_service::type","view_state","view_any_state","create_state","update_state","restore_state","restore_any_state","replicate_state","reorder_state","delete_state","delete_any_state","force_delete_state","force_delete_any_state","view_tender","view_any_tender","create_tender","update_tender","restore_tender","restore_any_tender","replicate_tender","reorder_tender","delete_tender","delete_any_tender","force_delete_tender","force_delete_any_tender","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_visit","view_any_visit","create_visit","update_visit","restore_visit","restore_any_visit","replicate_visit","reorder_visit","delete_visit","delete_any_visit","force_delete_visit","force_delete_any_visit"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
