<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissões
        $permissions = [
            // Permissões de gestão
            ['name' => 'manage_books', 'description' => 'Gerir livros (CRUD completo)'],
            ['name' => 'manage_authors', 'description' => 'Gerir autores (CRUD completo)'],
            ['name' => 'manage_publishers', 'description' => 'Gerir editoras (CRUD completo)'],
            ['name' => 'manage_users', 'description' => 'Gerir utilizadores'],

            // Permissões de visualização
            ['name' => 'view_books', 'description' => 'Visualizar livros'],
            ['name' => 'view_authors', 'description' => 'Visualizar autores'],
            ['name' => 'view_publishers', 'description' => 'Visualizar editoras'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }

        // Criar roles (apenas 3)
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador com todas as permissões',
                'permissions' => [
                    'manage_books', 'manage_authors', 'manage_publishers', 'manage_users',
                    'view_books', 'view_authors', 'view_publishers'
                ]
            ],
            [
                'name' => 'gestor_conteudo',
                'description' => 'Pode gerir livros, autores e editoras',
                'permissions' => [
                    'manage_books', 'manage_authors', 'manage_publishers',
                    'view_books', 'view_authors', 'view_publishers'
                ]
            ],
            [
                'name' => 'cliente',
                'description' => 'Pode apenas visualizar conteúdo',
                'permissions' => ['view_books', 'view_authors', 'view_publishers']
            ],
        ];

        foreach ($roles as $roleData) {
            $permissionsList = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Associar permissões ao role
            $permissionIds = Permission::whereIn('name', $permissionsList)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        // Criar admin user (apenas se não existir)
        $admin = User::firstOrCreate(
            ['email' => 'admin@biblioteca.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }

        // Criar gestor de conteúdo de exemplo
        $gestor = User::firstOrCreate(
            ['email' => 'gestor@biblioteca.com'],
            [
                'name' => 'Gestor de Conteúdo',
                'password' => Hash::make('password'),
            ]
        );

        $gestorRole = Role::where('name', 'gestor_conteudo')->first();
        if ($gestorRole && !$gestor->roles()->where('role_id', $gestorRole->id)->exists()) {
            $gestor->roles()->attach($gestorRole);
        }

        // Criar cliente de exemplo
        $cliente = User::firstOrCreate(
            ['email' => 'cliente@biblioteca.com'],
            [
                'name' => 'Cliente Exemplo',
                'password' => Hash::make('password'),
            ]
        );

        $clienteRole = Role::where('name', 'cliente')->first();
        if ($clienteRole && !$cliente->roles()->where('role_id', $clienteRole->id)->exists()) {
            $cliente->roles()->attach($clienteRole);
        }

        $this->command->info('✅ Roles e permissões criadas com sucesso!');
        $this->command->info('📧 Utilizadores de exemplo:');
        $this->command->info('   admin@biblioteca.com / password');
        $this->command->info('   gestor@biblioteca.com / password');
        $this->command->info('   cliente@biblioteca.com / password');
    }
}
