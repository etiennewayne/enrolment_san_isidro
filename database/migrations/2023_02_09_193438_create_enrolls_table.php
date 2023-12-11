<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrolls', function (Blueprint $table) {
            $table->id('enroll_id');

            $table->unsignedBigInteger('academic_year_id');
            $table->foreign('academic_year_id')->references('academic_year_id')->on('academic_years')
                ->onUpdate('cascade')->onDelete('cascade');


            $table->string('grade_level', 20)->nullable();
            $table->tinyInteger('learner_status')->default(0);

            $table->unsignedBigInteger('learner_id');
            $table->foreign('learner_id')->references('learner_id')->on('learners')
                ->onUpdate('cascade')->onDelete('cascade');

            //$table->string('scholarship')->nullable();
            //$table->unsignedBigInteger('semester_id')->default(0);
            //$table->unsignedBigInteger('track_id')->default(0);
            //$table->unsignedBigInteger('strand_id')->default(0);

            $table->unsignedBigInteger('semester_id')->default(0)->nullabe();
            $table->unsignedBigInteger('track_id')->default(0)->nullabe();
            $table->unsignedBigInteger('strand_id')->default(0)->nullabe();
            $table->unsignedBigInteger('section_id')->default(0)->nullabe();

            $table->date('date_enrolled')->nullable();
            
            $table->tinyInteger('is_enrolled')->default(0)
                ->nullable();
                
            //$table->unsignedBigInteger('section_id');
            //$table->string('section')->nullable();
            $table->string('administer_by', 50)->nullable();

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
        Schema::dropIfExists('enrolls');
    }
}
