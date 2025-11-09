<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. CREAR ROLES
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrador',
                'description' => 'Acceso total al sistema, gestión de usuarios y configuración',
                'level' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Gestión completa de operaciones y reportes',
                'level' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'gerente',
                'display_name' => 'Gerente',
                'description' => 'Visualización y gestión de reportes estratégicos',
                'level' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Supervisión de operaciones y registro de datos',
                'level' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'operador',
                'display_name' => 'Operador',
                'description' => 'Registro de datos de producción',
                'level' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'calidad',
                'display_name' => 'Inspector de Calidad',
                'description' => 'Gestión de inspecciones y control de calidad',
                'level' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'mantenimiento',
                'display_name' => 'Técnico de Mantenimiento',
                'description' => 'Gestión de tiempos muertos y mantenimiento',
                'level' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 2. CREAR PERMISOS
        $modules = [
            'dashboard' => ['view'],
            'equipment' => ['view', 'create', 'edit', 'delete'],
            'production' => ['view', 'create', 'edit', 'delete', 'export'],
            'quality' => ['view', 'create', 'edit', 'delete', 'export'],
            'downtime' => ['view', 'create', 'edit', 'delete', 'export'],
            'reports' => ['view', 'export'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'audit' => ['view'],
        ];

        $permissionId = 1;
        $permissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[] = [
                    'id' => $permissionId++,
                    'name' => "{$module}.{$action}",
                    'display_name' => ucfirst($action) . ' ' . ucfirst($module),
                    'module' => $module,
                    'action' => $action,
                    'description' => "Permiso para {$action} en módulo {$module}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('permissions')->insert($permissions);

        // 3. ASIGNAR PERMISOS A ROLES
        $rolePermissions = [
            // SuperAdmin: TODOS los permisos
            'superadmin' => range(1, count($permissions)),
            
            // Admin: Todos excepto gestión de usuarios y auditoría
            'admin' => array_filter($permissions, function($p) {
                return !in_array($p['module'], ['users', 'audit']);
            }),
            
            // Gerente: Solo lectura y reportes
            'gerente' => array_filter($permissions, function($p) {
                return in_array($p['action'], ['view', 'export']);
            }),
            
            // Supervisor: Lectura, creación y edición (no eliminación)
            'supervisor' => array_filter($permissions, function($p) {
                return in_array($p['action'], ['view', 'create', 'edit', 'export']) && 
                       !in_array($p['module'], ['users', 'audit', 'equipment']);
            }),
            
            // Operador: Solo producción (lectura y creación)
            'operador' => array_filter($permissions, function($p) {
                return $p['module'] === 'production' && in_array($p['action'], ['view', 'create']) ||
                       $p['module'] === 'dashboard' && $p['action'] === 'view';
            }),
            
            // Calidad: Solo calidad (completo) y lectura de dashboard
            'calidad' => array_filter($permissions, function($p) {
                return $p['module'] === 'quality' ||
                       ($p['module'] === 'dashboard' && $p['action'] === 'view');
            }),
            
            // Mantenimiento: Solo downtime (completo) y lectura de dashboard
            'mantenimiento' => array_filter($permissions, function($p) {
                return $p['module'] === 'downtime' ||
                       ($p['module'] === 'dashboard' && $p['action'] === 'view');
            }),
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = DB::table('roles')->where('name', $roleName)->first();
            
            foreach ($perms as $perm) {
                if (is_array($perm)) {
                    $permId = $perm['id'];
                } else {
                    $permId = $perm;
                }

                DB::table('role_permission')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. ACTUALIZAR USUARIO EXISTENTE Y CREAR SUPERADMIN
        $superAdminRole = DB::table('roles')->where('name', 'superadmin')->first();

        // Actualizar usuario test@example.com a SuperAdmin
        DB::table('users')->where('email', 'test@example.com')->update([
            'name' => 'SuperAdmin',
            'email' => 'admin@ecoplast.com',
            'role_id' => $superAdminRole->id,
            'is_active' => true,
            'position' => 'Super Administrador',
            'updated_at' => now(),
        ]);

        // Crear usuarios de ejemplo para cada rol
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $gerenteRole = DB::table('roles')->where('name', 'gerente')->first();
        $supervisorRole = DB::table('roles')->where('name', 'supervisor')->first();

        $exampleUsers = [
            [
                'name' => 'Carlos Administrador',
                'email' => 'carlos@ecoplast.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'position' => 'Administrador de Planta',
                'phone' => '+51 999 888 777',
            ],
            [
                'name' => 'María Gerente',
                'email' => 'maria@ecoplast.com',
                'password' => Hash::make('password'),
                'role_id' => $gerenteRole->id,
                'is_active' => true,
                'position' => 'Gerente de Operaciones',
                'phone' => '+51 999 888 666',
            ],
            [
                'name' => 'José Supervisor',
                'email' => 'jose@ecoplast.com',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole->id,
                'is_active' => true,
                'position' => 'Supervisor de Turno',
                'phone' => '+51 999 888 555',
            ],
        ];

        foreach ($exampleUsers as $user) {
            DB::table('users')->insert(array_merge($user, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        echo "✅ Roles y permisos creados exitosamente\n";
        echo "✅ Usuario SuperAdmin actualizado: admin@ecoplast.com\n";
        echo "✅ 3 usuarios de ejemplo creados\n";
    }
}

