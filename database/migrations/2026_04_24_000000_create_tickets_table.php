<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('movie_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('showtime_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_code')->unique();
            $table->text('qr_url')->nullable();
            $table->string('cinema');
            $table->string('hall')->nullable();
            $table->string('format')->nullable();
            $table->date('date');
            $table->string('time');
            $table->json('seats');
            $table->unsignedInteger('seat_count')->default(0);
            $table->decimal('seat_total', 10, 2)->default(0);
            $table->json('food_lines')->nullable();
            $table->decimal('food_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->string('payment_method');
            $table->string('promo_code', 50)->nullable();
            $table->string('status')->default('paid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}