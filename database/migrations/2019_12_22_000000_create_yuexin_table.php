<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\User;

class CreateYuexinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yuexin_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('directory');
            $table->string('ssd_directory');
            $table->json('args');
            $table->timestamps();
        });
        //创建admin用户
        $user=User::find(1);
        if(!$user){
            User::create(['name'=>'admin','email'=>'admin@admin.com','password'=>'administrator']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yuexin_projects');
    }
}
