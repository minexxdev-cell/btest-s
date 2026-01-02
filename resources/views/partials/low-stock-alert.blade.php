@if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
<div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-warning"></i> Low Stock Alert!</h4>
    <p>The following products have low stock (≤ 1):</p>
    <ul>
        @foreach($lowStockProducts as $product)
        <li>
            <strong>{{ $product->nama_produk }}</strong> 
            (Code: {{ $product->kode_produk }}) - 
            <span class="text-danger">Stock: {{ $product->stok }}</span>
        </li>
        @endforeach
    </ul>
</div>
@endif