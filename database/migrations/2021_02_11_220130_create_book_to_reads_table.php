<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookToReadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_to_reads', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('book_id');
            $table->string('book_title');
            $table->string('book_sub_title')->nullable();
            $table->mediumText('book_description')->nullable();
            $table->string('authors')->nullable();
            $table->string('categories')->nullable();
            $table->string('average_rating')->nullable();
            $table->string('book_img_url');
            $table->string('book_info_link');
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
        Schema::dropIfExists('book_to_reads');
    }
}
