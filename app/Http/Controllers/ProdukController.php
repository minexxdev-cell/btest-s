<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\StockMovement;
use PDF;

class ProdukController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $lowStockProducts = $this->getLowStockProducts();

        return view('produk.index', compact('kategori', 'lowStockProducts'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '<input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('stok_display', function ($produk) {
                $badge = $produk->stok <= 5 ? 'danger' : 'success';
                return '<span class="badge badge-'.$badge.'">'. $produk->stok .'</span>';
            })
            ->addColumn('stok_awal', function ($produk) {
                return format_uang($produk->stok_awal);
            })
            ->addColumn('stok_masuk', function ($produk) {
                return '<span class="text-success">+'. format_uang($produk->stok_masuk) .'</span>';
            })
            ->addColumn('stok_keluar', function ($produk) {
                return '<span class="text-danger">-'. format_uang($produk->stok_keluar) .'</span>';
            })
            ->addColumn('stok_rusak', function ($produk) {
                return '<span class="text-warning">-'. format_uang($produk->stok_rusak) .'</span>';
            })
            ->addColumn('stok_akhir', function ($produk) {
                return '<strong>'. format_uang($produk->stok_akhir) .'</strong>';
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="stockMovement(`'. route('produk.stock.movement', $produk->id_produk) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-exchange"></i></button>
                    <button type="button" onclick="stockHistory(`'. route('produk.stock.history', $produk->id_produk) .'`)" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-history"></i></button>
                    <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all', 'stok_display', 'stok_masuk', 'stok_keluar', 'stok_rusak', 'stok_akhir'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);
        
        // Set initial stock values
        $request['stok_awal'] = $request->stok ?? 0;
        $request['stok_akhir'] = $request->stok ?? 0;
        $request['tanggal_stock'] = now();

        $produk = Produk::create($request->all());

        // Record initial stock movement
        if ($request->stok > 0) {
            $produk->addStockMovement('masuk', $request->stok, 'Initial stock');
        }

        return response()->json('Data saved successfully', 200);
    }

    public function show($id)
    {
        $produk = Produk::find($id);
        return response()->json($produk);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data saved successfully', 200);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('product.pdf');
    }

    public function getLowStockProducts()
    {
        $lowStockProducts = Produk::where('stok', '<=', 5)
            ->leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->get();
        
        return $lowStockProducts;
    }

    // Stock Movement Form
    public function stockMovementForm($id)
    {
        $produk = Produk::with('kategori')->find($id);
        return view('produk.stock_movement', compact('produk'));
    }

    // Process Stock Movement
    public function processStockMovement(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:masuk,keluar,rusak,penyesuaian',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
            'lokasi' => 'nullable|string'
        ]);

        $produk = Produk::find($id);
        
        // Check if enough stock for outgoing movements
        if (in_array($request->tipe, ['keluar', 'rusak']) && $produk->stok < $request->jumlah) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $produk->addStockMovement(
            $request->tipe,
            $request->jumlah,
            $request->keterangan,
            $request->lokasi
        );

        return response()->json('Stock updated successfully', 200);
    }

    // Stock History
    public function stockHistory($id)
    {
        $produk = Produk::with('kategori')->find($id);
        $movements = StockMovement::where('id_produk', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('produk.stock_history', compact('produk', 'movements'));
    }

    // Print Stock Report
    public function printStockReport(Request $request)
    {
        $produk = Produk::with('kategori')->get();
        $tanggal = now()->format('d/m/Y');
        
        $pdf = PDF::loadView('produk.stock_report', compact('produk', 'tanggal'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('stock_report_' . date('Y-m-d') . '.pdf');
    }

    // Reset Daily Stock (to be called daily via cron)
    public function resetDailyStock()
    {
        $products = Produk::all();
        
        foreach ($products as $product) {
            $product->resetDailyStock();
        }

        return response()->json('Daily stock reset completed', 200);
    }
}