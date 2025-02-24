<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // To create a variable it is done with $ example $table
            $table->string('titulo');
            $table->text('descripcion');
            //nullable is used to say that it can have a null value
            $table->enum('estado', ["pendiente", "en_progreso", "completada"])->nullable();
            $table->date('fecha_vencimiento');
            // ForeignId() creates the column for the foreign key
            // constrained() establishes the relationship with another table
            $table->foreignId('user_id')->constrained('users');
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
        Schema::dropIfExists('tasks');
    }
}
