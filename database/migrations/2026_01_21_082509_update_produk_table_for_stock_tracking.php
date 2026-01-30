<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProdukTableForStockTracking extends Migration
{
    public function up()
    {
        Schema::table('produk', function (Blueprint $table) {
            // Add new columns for stock tracking
            $table->integer('stok_awal')->default(0)->after('stok'); // Opening stock
            $table->integer('stok_masuk')->default(0)->after('stok_awal'); // Stock in
            $table->integer('stok_keluar')->default(0)->after('stok_masuk'); // Stock out
            $table->integer('stok_rusak')->default(0)->after('stok_keluar'); // Damaged stock
            $table->integer('stok_akhir')->default(0)->after('stok_rusak'); // Closing stock
            $table->date('tanggal_stock')->nullable()->after('stok_akhir'); // Stock date
        });
    }

    public function down()
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn([
                'stok_awal',
                'stok_masuk',
                'stok_keluar',
                'stok_rusak',
                'stok_akhir',
                'tanggal_stock'
            ]);
        });
    }
}