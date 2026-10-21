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
        Schema::create('solicitacoes_procedimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->foreignId('solicitado_por')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['pendente', 'aprovada', 'recusada'])->default('pendente');
            $table->foreignId('decidido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decidido_em')->nullable();
            $table->string('motivo', 500)->nullable();
            $table->foreignId('procedimento_id')->nullable()->unique()->constrained('procedimentos')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['solicitado_por', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_procedimentos');
    }
};
