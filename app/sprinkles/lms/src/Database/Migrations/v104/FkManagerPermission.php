<?php
namespace UserFrosting\Sprinkle\Lms\Database\Migrations\v104;

use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\Sprinkle\Core\Database\Migration;

class FkManagerPermission extends Migration
{
    public static $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\PermissionsTable',
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\RolesTable'
    ];

    public function up()
    {
        // Add default permissions
        $permissions = $this->getPermissions();

        foreach ($permissions as $id => $permission) {
            $slug = $permission->slug;
            $conditions = $permission->conditions;
            // Skip if a permission with the same slug and conditions has already been added
            if (!Permission::where('slug', $slug)->where('conditions', $conditions)->first()) {
                $permission->save();
            }
        }
    }

    public function down()
    {
        foreach ($this->getPermissions() as $id => $permissionData) {
            $permission = Permission::where($permissionData)->first();
            $permission->delete();
        }
    }

    protected function getPermissions()
    {
        return [
            'fk_manager' => new Permission([
                'slug' => 'fk_manager',
                'name' => 'FK Manager',
                'conditions' => 'always()',
                'description' => 'Permission to keep the site running.'
            ])
        ];
    }
}