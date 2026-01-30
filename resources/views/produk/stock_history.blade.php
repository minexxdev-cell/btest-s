@extends('layouts.master')

@section('title')
    Stock Movement History
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Stock History</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Stock Movement History - {{ $produk->nama_produk }}</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('produk.index') }}" class="btn btn-sm btn-default btn-flat">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-cube"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current Stock</span>
                                <span class="info-box-number">{{ $produk->stok }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-arrow-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Stock In</span>
                                <span class="info-box-number">{{ $produk->stok_masuk }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-arrow-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Stock Out</span>
                                <span class="info-box-number">{{ $produk->stok_keluar }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Stock Before</th>
                            <th>Stock After</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $index => $movement)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($movement->tanggal)->format('d/m/Y') }} {{ $movement->created_at->format('H:i') }}</td>
                            <td>
                                @if ($movement->tipe == 'masuk')
                                    <span class="label label-success">Stock In</span>
                                @elseif ($movement->tipe == 'keluar')
                                    <span class="label label-danger">Stock Out</span>
                                @elseif ($movement->tipe == 'rusak')
                                    <span class="label label-warning">Damaged</span>
                                @else
                                    <span class="label label-info">Adjustment</span>
                                @endif
                            </td>
                            <td>
                                @if (in_array($movement->tipe, ['keluar', 'rusak']))
                                    <span class="text-danger">-{{ $movement->jumlah }}</span>
                                @else
                                    <span class="text-success">+{{ $movement->jumlah }}</span>
                                @endif
                            </td>
                            <td>{{ $movement->stok_sebelum }}</td>
                            <td><strong>{{ $movement->stok_sesudah }}</strong></td>
                            <td>{{ $movement->lokasi ?? '-' }}</td>
                            <td>{{ $movement->keterangan ?? '-' }}</td>
                            <td>{{ $movement->user->name ?? 'System' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection