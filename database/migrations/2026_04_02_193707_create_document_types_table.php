<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->boolean('requires_verification')->default(true);
            $table->boolean('is_confidential_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};