<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->text('proof');
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->index(['report_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('claims'); }
};