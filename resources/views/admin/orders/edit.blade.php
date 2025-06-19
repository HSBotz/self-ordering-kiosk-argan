@extends('admin.layouts.app')

@section('title', 'Edit Pesanan #' . $order->order_number)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Edit Status Pesanan</span>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="order_number" class="form-label">No. Pesanan</label>
                    <input type="text" class="form-control" id="order_number" value="{{ $order->order_number }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="created_at" class="form-label">Tanggal Pesanan</label>
                    <input type="text" class="form-control" id="created_at" value="{{ $order->created_at->format('d M Y H:i') }} {{ \App\Models\SiteSetting::getValue('store_timezone', 'WIB') }}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="customer_name" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="customer_name" value="{{ $order->customer_name ?? 'Anonim' }}" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="total_amount" class="form-label">Total Pembayaran</label>
                    <input type="text" class="form-control" id="total_amount" value="Rp {{ number_format($order->total_amount, 0, ',', '.') }}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="order_type" class="form-label">Tipe Pesanan</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="{{ $order->order_type == 'dine-in' ? 'fas fa-utensils' : 'fas fa-shopping-bag' }}"></i>
                        </span>
                        <input type="text" class="form-control" id="order_type" value="{{ $order->order_type == 'dine-in' ? 'Dine In' : 'Take Away' }}" readonly>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="payment_method" class="form-label">Metode Pembayaran</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            @if($order->payment_method == 'cash')
                                <i class="fas fa-money-bill-wave"></i>
                            @elseif($order->payment_method == 'qris')
                                <i class="fas fa-qrcode"></i>
                            @else
                                <i class="fas fa-credit-card"></i>
                            @endif
                        </span>
                        <input type="text" class="form-control" id="payment_method" value="{{ ucfirst($order->payment_method ?? 'Tunai') }}" readonly>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Catatan</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ $order->notes }}</textarea>
                @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
