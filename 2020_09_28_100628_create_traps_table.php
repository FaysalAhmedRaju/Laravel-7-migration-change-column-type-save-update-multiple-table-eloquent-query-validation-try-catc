<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traps', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('name')->default('');
            $table->string('address')->default('');
            $table->timestamp('date_time')->default(null);
            $table->float('latitude')->default(null);
            $table->float('longitude')->default(null);
            $table->bigInteger('clients_id')->unsigned()->nullable();
            $table->foreign('clients_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('customers_id')->unsigned()->nullable();
            $table->foreign('customers_id')->references('id')->on('customers')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('devices_id')->unsigned()->nullable();
            $table->foreign('devices_id')->references('id')->on('devices')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traps');
    }
}
