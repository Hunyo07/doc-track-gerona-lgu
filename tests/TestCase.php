<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    protected function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        
        // Create role if it doesn't exist
        $role = Role::firstOrCreate(['name' => $roleName]);
        
        // Assign basic permissions based on role
        $permissions = $this->getPermissionsForRole($roleName);
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }
        
        $user->assignRole($role);
        
        return $user;
    }
    
    protected function getPermissionsForRole(string $roleName): array
    {
        return match($roleName) {
            'admin' => [
                'create documents', 'view documents', 'update documents', 'delete documents',
                'forward documents', 'receive documents', 'sign documents', 'track documents'
            ],
            'bac_member' => [
                'create documents', 'view documents', 'update documents',
                'forward documents', 'receive documents', 'sign documents', 'track documents'
            ],
            'user' => [
                'create documents', 'view documents', 'update documents', 'track documents'
            ],
            default => ['view documents', 'track documents']
        };
    }
}
