@extends('admin.layouts.app')

@section('title', 'Detail #' . $order->order_number)

@section('styles')
<style>
    /* Gaya untuk tampilan kompak */
    .card {
        margin-bottom: 0.75rem;
    }
    .card-header {
        padding: 0.5rem 1rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    h5 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .table th, .table td {
        padding: 0.4rem 0.5rem;
        font-size: 0.85rem;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .form-control, .form-select {
        padding: 0.3rem 0.5rem;
        font-size: 0.85rem;
    }
    .form-label {
        margin-bottom: 0.25rem;
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="row g-2">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Detail Pesanan</span>
                <div>
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm me-1">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="card card-body bg-light p-2">
                            <h5 class="mb-2">Informasi Pesanan</h5>
                            <p class="mb-1 small"><strong>No. Pesanan:</strong> {{ $order->order_number }}</p>
                            <p class="mb-1 small"><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1 small">
                                <strong>Status:</strong>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info">Diproses</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @endif
                            </p>
                            <p class="mb-1 small">
                                <strong>Tipe:</strong>
                                @if($order->order_type == 'dine-in')
                                    <span class="badge bg-info"><i class="fas fa-utensils me-1"></i> Dine In</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-shopping-bag me-1"></i> Take Away</span>
                                @endif
                                <strong class="ms-2">Bayar:</strong> {{ ucfirst($order->payment_method ?? 'Tunai') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-body bg-light p-2">
                            <h5 class="mb-2">Informasi Pelanggan</h5>
                            <p class="mb-1 small"><strong>Nama:</strong> {{ $order->customer_name ?? 'Anonim' }}</p>
                            @if($order->notes)
                            <p class="mb-0 small"><strong>Catatan:</strong> {{ $order->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <h5 class="mb-2">Item Pesanan</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center" width="100">Varian</th>
                                <th class="text-end" width="80">Harga</th>
                                <th class="text-center" width="60">Qty</th>
                                <th class="text-end" width="100">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td class="small">{{ $item->product->name ?? 'Produk tidak tersedia' }}</td>
                                <td class="text-center">
                                    @if($item->variant_type == 'hot')
                                        <span class="badge bg-danger"><i class="fas fa-fire me-1"></i>Panas</span>
                                    @elseif($item->variant_type == 'ice')
                                        <span class="badge bg-info"><i class="fas fa-snowflake me-1"></i>Dingin</span>
                                    @else
                                        @if($item->product && $item->product->category && !$item->product->category->has_variants)
                                            <span class="badge bg-secondary"><i class="fas fa-minus me-1"></i>Tidak ada</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-question-circle me-1"></i>-</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end small">{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center small">{{ $item->quantity }}</td>
                                <td class="text-end small">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th class="text-end">{{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label for="notes" class="form-label">Catatan Admin</label>
                        <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2">{{ $order->notes }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save me-1"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pengaturan Varian Manual -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-mug-hot me-1"></i> Pengaturan Varian</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Atur varian untuk setiap item:</p>

                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_variants" value="1">

                    @foreach($order->orderItems as $item)
                    <div class="mb-2 border rounded p-2">
                        <div class="fw-bold small mb-1">{{ $item->product->name ?? 'Produk #'.$item->product_id }}</div>

                        @if($item->product && $item->product->category && !$item->product->category->has_variants)
                        <!-- Produk dari kategori tanpa varian -->
                        <div class="alert alert-secondary py-1 small mb-0">
                            Produk ini dari kategori tanpa varian ({{ $item->product->category->name }})
                        </div>
                        <input type="hidden" name="variants[{{ $item->id }}]" value="">
                        @else
                        <!-- Produk dengan varian -->
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="variants[{{ $item->id }}]" value="hot" id="variant_hot_{{ $item->id }}" {{ $item->variant_type == 'hot' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger btn-sm py-0" for="variant_hot_{{ $item->id }}"><i class="fas fa-fire me-1"></i>Panas</label>

                            <input type="radio" class="btn-check" name="variants[{{ $item->id }}]" value="ice" id="variant_ice_{{ $item->id }}" {{ $item->variant_type == 'ice' ? 'checked' : '' }}>
                            <label class="btn btn-outline-info btn-sm py-0" for="variant_ice_{{ $item->id }}"><i class="fas fa-snowflake me-1"></i>Dingin</label>

                            <input type="radio" class="btn-check" name="variants[{{ $item->id }}]" value="" id="variant_none_{{ $item->id }}" {{ $item->variant_type === null ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary btn-sm py-0" for="variant_none_{{ $item->id }}"><i class="fas fa-minus-circle me-1"></i>Tidak ada</label>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-save me-1"></i> Simpan Varian
                        </button>
                    </div>
                </form>

                <hr class="my-2">

                <div class="d-flex justify-content-center gap-2 mt-2">
                    <a href="{{ route('admin.orders.fix-variants', $order->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-magic me-1"></i> Perbaikan Otomatis
                    </a>

                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#variantHelpModal">
                        <i class="fas fa-question-circle me-1"></i> Bantuan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bantuan Varian -->
<div class="modal fade" id="variantHelpModal" tabindex="-1" aria-labelledby="variantHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="variantHelpModalLabel">Bantuan Varian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                <div class="mb-2">
                    <span class="badge bg-danger"><i class="fas fa-fire me-1"></i> Panas</span>
                    <span>- Minuman disajikan panas</span>
                </div>
                <div class="mb-2">
                    <span class="badge bg-info"><i class="fas fa-snowflake me-1"></i> Dingin</span>
                    <span>- Minuman disajikan dengan es</span>
                </div>
                <div class="mb-2">
                    <span class="badge bg-secondary"><i class="fas fa-minus me-1"></i> Tidak ada</span>
                    <span>- Produk dari kategori tanpa varian</span>
                </div>
                <div class="mb-2">
                    <span class="badge bg-secondary"><i class="fas fa-question-circle me-1"></i> -</span>
                    <span>- Varian belum ditentukan</span>
                </div>
                <hr>
                <p class="mb-0">
                    <i class="fas fa-info-circle me-1 text-info"></i> Produk dari kategori tanpa varian (seperti makanan) tidak akan menampilkan opsi varian.
                </p>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
