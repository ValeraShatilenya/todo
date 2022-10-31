<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->foreignId('user_id');
            $table->string('title');
            $table->text('description');
            $table->tinyInteger('status');
            $table->timestamp('completed')->nullable();
            $table->foreignId('completed_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('completed_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_tasks');
    }
}
