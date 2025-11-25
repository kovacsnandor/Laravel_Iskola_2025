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
        Schema::create('schoolclasses', function (Blueprint $table) {
            // $table->integer('id')->autoIncrement();
            // $table->primary('id');
            $table->id(); //Bigint, Unsigned
            $table->string('osztalyNev', 75)->notNull();
            $table->unique('osztalyNev');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schoolclasses');
    }
};
