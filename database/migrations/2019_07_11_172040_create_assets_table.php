<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            $table->string('member_id', 36);
            $table->string('asset_number')->unique();
            $table->string('title');
            $table->string('description');
            $table->string('valuation_date');
            $table->string('valued_by');
            $table->string('valuer_phone');
            $table->string('valuation_amount');
            $table->string('location');
            $table->string('registration_number')->nullable();
            $table->string('registered_to')->nullable();
            $table->string('condition');
            $table->string('notes');

            $table->softDeletes();
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
        Schema::dropIfExists('assets');
    }
}