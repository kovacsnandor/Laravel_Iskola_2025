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
        Schema::create('students', function (Blueprint $table) {
            // $table->integer('id')->autoIncrement();
            // $table->primary('id');
            $table->id();
            $table->string('diakNev', 75)->notNull();
            // $table->integer('schoolclassId')->nullable()->default(null);
            // $table->foreign('schoolclassId') // Az idegen kulcs oszlopa
            //       ->references('id')         // A hivatkozott oszlop a szülő táblában
            //       ->on('schoolclasses')      // A szülő tábla neve
            //       ->onDelete('restrict');    // Ha hivatkoznak rá, a törlés blokkolva van.

            //1. Létrehozza a mezőt 2. Feltételezi hogy a schoolclasses tábla kulcsa id, és megycsinálja a kapcsolatot is
            $table->foreignId('schoolclassId')->constrained('schoolclasses')->onDelete('cascade'); 

            $table->boolean('neme')->default(false);
            $table->string('iranyitoszam', 5)->nullable()->default(null);
            $table->string('lakHelyseg', 75)->nullable()->default(null);
            $table->string('lakCim', 100)->nullable()->default(null);
            $table->string('szulHelyseg', 75)->nullable()->default(null);
            $table->date('szulDatum')->nullable()->default(null);
            $table->string('igazolvanyszam', 25)->nullable()->default(null);
            $table->decimal('atlag', 2, 1)->default(0.0);
            $table->decimal('osztondij', 10, 0)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
