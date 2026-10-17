<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('sobrenome', 100);
            $table->date('data_nascimento');
            $table->string('bairro', 100);
            $table->string('telefone', 20);
            $table->foreignId('dentista_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->index(['dentista_id', 'sobrenome', 'nome']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
