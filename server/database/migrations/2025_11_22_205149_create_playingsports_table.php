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
        Schema::create('playingsports', function (Blueprint $table) {
            // $table->integer('studentId')->notNull();
            // $table->integer('sportId')->notNull();
            // $table->foreign('studentId')
            //     ->references('id')        
            //     ->on('students')
            //     ->onDelete('restrict');  
            // $table->foreign('sportId')
            //     ->references('id')         
            //     ->on('sports')
            //     ->onDelete('restrict');

            // $table->foreignId('studentId')->constrained('students')->onDelete('cascade');
            // $table->foreignId('sportId')->constrained('sports')->onDelete('restrict');

            // $table->primary(['studentId', 'sportId']);
            // $table->timestamps();

            $table->id(); // Hozzáadja az automatikusan növekvő BIGINT Primary Key oszlopot
            
            $table->foreignId('studentId')->constrained('students')->onDelete('cascade');
            $table->foreignId('sportId')->constrained('sports')->onDelete('restrict');
            
            $table->unique(['studentId', 'sportId']);
            
            // Időbélyegek
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playingsports');
    }
};
