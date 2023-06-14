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
        Schema::create('brevets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nro');
            $table->date('expiration_date');
            $table->date('broadcast_date');
            $table->char('category');
            $table->string('photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brevets');
    }
};
