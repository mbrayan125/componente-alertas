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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_system_id');
            $table->string('name_verb');
            $table->string('name_complement');
            $table->string('token', 32);
            $table->unsignedInteger('version')->unsigned();
            $table->string('bpmn_filepath');
            $table->timestamps();
            $table->foreign('target_system_id')->references('id')->on('target_systems');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
