<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remover duplicados existentes antes de adicionar unique
        DB::statement('
            DELETE FROM autors
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM autors
                GROUP BY LOWER(nome)
            )
        ');

        DB::statement('
            DELETE FROM editoras
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM editoras
                GROUP BY LOWER(nome)
            )
        ');

        // Adicionar unique constraints
        Schema::table('autors', function (Blueprint $table) {
            $table->unique('nome');
        });

        Schema::table('editoras', function (Blueprint $table) {
            $table->unique('nome');
        });
    }

    public function down(): void
    {
        Schema::table('autors', function (Blueprint $table) {
            $table->dropUnique(['nome']);
        });

        Schema::table('editoras', function (Blueprint $table) {
            $table->dropUnique(['nome']);
        });
    }
};
