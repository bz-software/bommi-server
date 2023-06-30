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
        Schema::create('user', function (Blueprint $table) {
            $table->increments('us_id');
            $table->string('us_nome', 300);
            $table->date('us_data_nascimento');
            $table->string('us_cpf', 11);
            $table->integer('us_cargo');
            $table->string('us_email', 300);
            $table->string('us_senha', 200);
            $table->integer('us_permissoes_id');
            $table->string('us_avatar_url', 500);
            $table->tinyInteger('us_excluido')->default(0);
            $table->timestamps();
            
            $table->index('us_cargo');
            $table->index('us_permissoes_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
