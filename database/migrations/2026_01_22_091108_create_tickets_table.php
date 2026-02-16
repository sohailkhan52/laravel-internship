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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("description");
            $table->timestamps();
            // admin assigned by the ticket
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            // Member assigned to the ticket
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
