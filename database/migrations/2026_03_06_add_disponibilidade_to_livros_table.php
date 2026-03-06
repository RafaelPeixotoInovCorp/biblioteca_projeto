<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            if (!Schema::hasColumn('livros', 'disponivel')) {
                $table->boolean('disponivel')->default(true);
            }
            if (!Schema::hasColumn('livros', 'requisicoes_count')) {
                $table->integer('requisicoes_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn(['disponivel', 'requisicoes_count']);
        });
    }
};
