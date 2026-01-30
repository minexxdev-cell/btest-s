<!DOCTYPE html>
<html>
<head>
    <title>Stock Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>FICHE DE STOCKS - STOCK REPORT</h2>
        <p>Date: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="8%">Code</th>
                <th width="15%">Article/Product</th>
                <th width="10%">Category</th>
                <th width="8%">Opening Stock</th>
                <th width="8%">Stock In</th>
                <th width="8%">Stock Out</th>
                <th width="8%">Damaged</th>
                <th width="8%">Closing Stock</th>
                <th width="10%">Buy Price</th>
                <th width="14%">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalValue = 0;
            @endphp
            @foreach ($produk as $index => $item)
            @php
                $value = $item->stok_akhir * $item->harga_beli;
                $totalValue += $value;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->kode_produk }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                <td class="text-right">{{ $item->stok_awal }}</td>
                <td class="text-right">{{ $item->stok_masuk }}</td>
                <td class="text-right">{{ $item->stok_keluar }}</td>
                <td class="text-right">{{ $item->stok_rusak }}</td>
                <td class="text-right"><strong>{{ $item->stok_akhir }}</strong></td>
                <td class="text-right">{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($value, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="text-right"><strong>Total Stock Value:</strong></td>
                <td class="text-right"><strong>{{ number_format($totalValue, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="totals">
        <p>Summary:</p>
        <ul>
            <li>Total Products: {{ $produk->count() }}</li>
            <li>Total Stock Value: {{ number_format($totalValue, 0, ',', '.') }}</li>
            <li>Low Stock Items: {{ $produk->where('stok', '<=', 5)->count() }}</li>
        </ul>
    </div>
</body>
</html>