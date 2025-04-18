<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category')->nullable();
            $table->boolean('is_completed');
            $table->timestamp('due_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        
            $table->fullText(['title', 'description']); 
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
