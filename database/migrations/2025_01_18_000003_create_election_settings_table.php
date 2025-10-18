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
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->boolean('allow_abstain')->default(false); // Izinkan golput
            $table->boolean('show_results_after_vote')->default(true); // Tampilkan hasil setelah vote
            $table->boolean('require_confirmation')->default(true); // Butuh konfirmasi sebelum submit
            $table->boolean('allow_vote_change')->default(false); // Izinkan ubah pilihan
            $table->integer('max_votes_per_voter')->default(1); // Maksimal pilih berapa kandidat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_settings');
    }
};
