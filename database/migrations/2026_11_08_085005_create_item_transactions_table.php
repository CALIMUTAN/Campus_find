<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete();

            $table->foreignId('claim_id')
                ->nullable()
                ->constrained('claims')
                ->nullOnDelete();

            $table->foreignId('recorded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('type'); // drop_off or pickup

            $table->boolean('identity_verified')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('recorded_at')->useCurrent();

            $table->timestamps();

            // Prevent duplicate transactions for the same item and type.
            $table->unique(['report_id', 'type']);

            // A claim can only have one pickup transaction.
            $table->unique('claim_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transactions');
    }
};
