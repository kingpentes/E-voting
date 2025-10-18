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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['organizer', 'voter', 'admin'])->default('voter')->after('email');
            $table->string('id_number')->nullable()->unique()->after('role'); // NIK/NIM untuk voter
            $table->string('face_photo')->nullable()->after('id_number'); // Foto wajah untuk verifikasi
            $table->string('organization')->nullable()->after('face_photo'); // Institusi/Organisasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'id_number', 'face_photo', 'organization']);
        });
    }
};
