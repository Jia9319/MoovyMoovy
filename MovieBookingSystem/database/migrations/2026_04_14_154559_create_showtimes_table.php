<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowtimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('showtimes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('movie_id')->constrained()->onDelete('cascade');
        $table->foreignId('cinema_id')->constrained()->onDelete('cascade');
        $table->dateTime('show_time');
        $table->decimal('price', 8, 2)->default(0.00);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('showtimes');
    }
}
