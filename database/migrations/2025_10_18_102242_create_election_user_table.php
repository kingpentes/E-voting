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
        Schema::create('election_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('access_code_used', 8); // Code yang digunakan saat registrasi
            $table->timestamp('joined_at')->nullable(); // Kapan voter join
            $table->timestamps();
            
            // Unique constraint: satu voter hanya bisa join satu election dengan code yang sama sekali
            $table->unique(['election_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_user');
    }
};
