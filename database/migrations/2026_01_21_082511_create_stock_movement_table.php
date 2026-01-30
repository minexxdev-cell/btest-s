<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movement', function (Blueprint $table) {
            $table->id('id_movement');
            $table->unsignedInteger('id_produk'); // Match your produk table type
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar', 'rusak', 'penyesuaian']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('keterangan')->nullable();
            $table->string('lokasi')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            
            // Add indexes for better performance (instead of foreign key)
            $table->index('id_produk');
            $table->index('tanggal');
            $table->index('tipe');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movement');
    }
}