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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('event_type_id');
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
            $table->string('event_for')->comment('MYSELF, OTHER');
            $table->text('event_condition')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('created_to');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
