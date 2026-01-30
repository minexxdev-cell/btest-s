<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'id_produk', 'id_produk');
    }

    // Calculate closing stock
    public function calculateClosingStock()
    {
        $this->stok_akhir = $this->stok_awal + $this->stok_masuk - $this->stok_keluar - $this->stok_rusak;
        $this->stok = $this->stok_akhir; // Update main stock field
        $this->save();
        
        return $this->stok_akhir;
    }

    // Add stock movement and update stock
    public function addStockMovement($tipe, $jumlah, $keterangan = null, $lokasi = null)
    {
        $stok_sebelum = $this->stok;
        
        // Update stock based on movement type
        switch ($tipe) {
            case 'masuk':
                $this->stok_masuk += $jumlah;
                $this->stok += $jumlah;
                break;
            case 'keluar':
                $this->stok_keluar += $jumlah;
                $this->stok -= $jumlah;
                break;
            case 'rusak':
                $this->stok_rusak += $jumlah;
                $this->stok -= $jumlah;
                break;
            case 'penyesuaian':
                $this->stok = $jumlah;
                break;
        }
        
        $this->stok_akhir = $this->stok;
        $this->save();
        
        // Record movement
        StockMovement::create([
            'id_produk' => $this->id_produk,
            'tanggal' => now(),
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stok_sebelum,
            'stok_sesudah' => $this->stok,
            'keterangan' => $keterangan,
            'lokasi' => $lokasi,
            'id_user' => auth()->id()
        ]);
        
        return $this;
    }

    // Reset daily stock (call this at start of day)
    public function resetDailyStock()
    {
        $this->stok_awal = $this->stok_akhir;
        $this->stok_masuk = 0;
        $this->stok_keluar = 0;
        $this->stok_rusak = 0;
        $this->tanggal_stock = now();
        $this->save();
        
        return $this;
    }
}