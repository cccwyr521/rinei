<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJyShippingTable extends Migration
{
    /**
     * Run the migrations.
     *配送方式表
     * @return void
     */
    public function up()
    {
        Schema::create('jy_shipping', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shipping_name',20)->default('')->comment('配送名字');
            $table->string('shipping_desc',30)->default('')->comment('配送简单描述');
            $table->integer('fee')->default(0)->comment('配送费用');
            $table->enum('status',[1,2])->default('1')->comment('状态1可用2禁用');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jy_shipping');
    }
}
