<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /**
    # Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different locations

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cinema_name');
            $table->string('owner_name');
            $table->string('contact_details');
            $table->string('year_established');
        });

        Schema::create('cinema_halls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreign('cinema_id')->references('id')->on('cinemas')->onDelete('cascade');
            $table->string('address');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_name');
        });

        Schema::create('movies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('movie_name');
            $table->timestamp('release_date');
            $table->smallInteger('duration_in_minutes');
        });

        Schema::create('movie_shows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('movie_id');
            $table->integer('cinema_hall_id');
            $table->unsignedFloat('commission_percentage');
            $table->boolean('is_booked_out');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
        });


        Schema::create('seat_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('price');
            $table->string('seat_type_name');
        });

        Schema::create('show_seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreign('show_id')->references('id')->on('movie_shows')->onDelete('cascade');
            $table->foreign('seat_type')->references('id')->on('seat_types')->onDelete('cascade');
            $table->boolean('is_available');
        });


        Schema::create('show_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreign('show_id')->references('id')->on('movie_shows')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamp('booked_on');
            $table->unsignedFloat('amount');
            $table->foreign('seat_id')->references('id')->on('show_seats')->onDelete('cascade');
        });


        Schema::create('show_bookings_to_seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreign('show_booking_id')->references('id')->on('show_bookings')->onDelete('cascade');
            $table->foreign('show_seat_id')->references('id')->on('show_seats')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cinemas');
        Schema::dropIfExists('cinema_halls');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('movie_shows');
        Schema::dropIfExists('seat_types');
        Schema::dropIfExists('show_seats');
        Schema::dropIfExists('show_bookings');
    }
}
