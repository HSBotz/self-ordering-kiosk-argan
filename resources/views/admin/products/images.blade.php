@extends('admin.layouts.app')

@section('title', 'Kelola Gambar Produk')

@section('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .product-image-container {
        height: 200px;
        overflow: hidden;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-image-placeholder {
        font-size: 48px;
        color: var(--primary-color);
    }

    .image-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
    }

    .image-actions .btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Kelola Gambar Produk</span>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Produk
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="product-card">
                        <div class="position-relative">
                            <div class="product-image-container">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" class="product-image" alt="{{ $product->name }}">
                                    <div class="image-actions">
                                        <form action="{{ route('admin.products.images.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus gambar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="p-3">
                            <h5>{{ $product->name }}</h5>
                            <p class="text-muted small">{{ $product->category->name ?? 'Tidak ada kategori' }}</p>

                            <form action="{{ route('admin.products.images.store') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="mb-3">
                                    <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror" name="image" accept="image/*" required>
                                    <small class="form-text text-muted">Format: JPG, PNG. Maks: 200MB.</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-upload me-1"></i> Upload Gambar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-coffee fa-3x mb-3 text-muted"></i>
                        <p>Belum ada produk yang tersedia.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Produk Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
