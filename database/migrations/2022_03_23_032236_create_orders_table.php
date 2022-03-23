<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('waiter_id')->default('0');
            $table->enum('waiter_approval', ['0', '1'])->default('0')->comment('0 - Not Approved, 1 - Approved');;
            $table->enum('order_status', ['Order Created', 'Order Taken', 'Order Prepairing', 'Order Delivered', 'Order Cancelled', 'Payment Done'])->default('Order Created');
            $table->decimal('order_total_amount', $precision = 8, $scale = 2)->default('0.00');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
