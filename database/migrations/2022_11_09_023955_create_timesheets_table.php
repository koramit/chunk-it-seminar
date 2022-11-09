<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('division');
            $table->string('type');
            $table->string('org_id')->index();
            $table->string('full_name');
            $table->string('position');
            $table->string('work_hour')->nullable();
            $table->string('flex_time_note')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('remark')->nullable();
            $table->string('reason')->nullable();
            $table->string('summary')->nullable();
            $table->date('datestamp');
            $table->unsignedTinyInteger('flex_time_use')->default(0);
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
        Schema::dropIfExists('timesheets');
    }
};
