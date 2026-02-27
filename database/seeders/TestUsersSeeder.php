<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Utilizador para gestor de livros
        $userLivros = User::create([
            'name' => 'Gestor de Livros',
            'email' => 'livros@biblioteca.com',
            'password' => Hash::make('password'),
        ]);
        $userLivros->roles()->attach(Role::where('name', 'gestor_livros')->first());

        // Utilizador para gestor de autores
        $userAutores = User::create([
            'name' => 'Gestor de Autores',
            'email' => 'autores@biblioteca.com',
            'password' => Hash::make('password'),
        ]);
        $userAutores->roles()->attach(Role::where('name', 'gestor_autores')->first());

        // Utilizador para gestor de editoras
        $userEditoras = User::create([
            'name' => 'Gestor de Editoras',
            'email' => 'editoras@biblioteca.com',
            'password' => Hash::make('password'),
        ]);
        $userEditoras->roles()->attach(Role::where('name', 'gestor_editoras')->first());

        // Utilizador para gestor de conteúdo (tudo)
        $userConteudo = User::create([
            'name' => 'Gestor de Conteúdo',
            'email' => 'conteudo@biblioteca.com',
            'password' => Hash::make('password'),
        ]);
        $userConteudo->roles()->attach(Role::where('name', 'gestor_conteudo')->first());

        $this->command->info('Utilizadores de teste criados com sucesso!');
    }
}
