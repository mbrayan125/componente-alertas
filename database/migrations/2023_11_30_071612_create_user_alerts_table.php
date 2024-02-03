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
        Schema::create('user_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_instance_history_id');
            $table->string('type');
            $table->string('visual_representation');
            $table->string('color');
            $table->string('icon');
            $table->string('title');
            $table->text('message');
            $table->json('actions');
            $table->string('alert_moment');
            $table->timestamps();

            $table->foreign('process_instance_history_id')->on('process_instances_history')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_alerts');
    }
};
