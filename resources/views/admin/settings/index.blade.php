@extends('admin.layouts.app')

@section('title', 'Pengaturan Situs')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Pengaturan Situs</span>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Pesan Coming Soon -->
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading">Pengembangan Berlangsung!</h5>
                    <p class="mb-0">Beberapa fitur pengaturan masih dalam tahap pengembangan dan akan segera hadir. Terima kasih atas kesabaran Anda.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-info-circle fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Tentang Aplikasi</h5>
                        <p class="card-text">Informasi tentang versi aplikasi, panduan penggunaan, dan hak cipta.</p>
                        <a href="{{ route('admin.settings.about') }}" class="btn btn-primary">
                            <i class="fas fa-info-circle me-1"></i> Lihat
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-file-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Pengaturan Footer</h5>
                        <p class="card-text">Kelola konten footer website seperti informasi kontak, jam buka, dan media sosial.</p>
                        <a href="{{ route('admin.settings.footer') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i> Kelola
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-store fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Informasi Toko</h5>
                        <p class="card-text">Kelola informasi toko seperti nama, logo, dan informasi kontak utama.</p>
                        <a href="{{ route('admin.settings.store') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i> Kelola
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-palette fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Tampilan</h5>
                        <p class="card-text">Kustomisasi tampilan website seperti warna tema dan font.</p>
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-clock me-1"></i> Segera Hadir
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-credit-card fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Pembayaran</h5>
                        <p class="card-text">Kelola pengaturan pembayaran seperti pajak, mata uang, dan metode pembayaran.</p>
                        <a href="{{ route('admin.settings.payment') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i> Kelola
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-dashed">
                    <div class="card-body text-center position-relative">
                        <div class="coming-soon-badge">
                            <span class="badge bg-warning text-dark">Coming Soon</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-3x text-secondary opacity-50"></i>
                        </div>
                        <h5 class="card-title text-muted">Notifikasi Email</h5>
                        <p class="card-text text-muted">Kelola template dan pengaturan email notifikasi untuk pesanan dan akun.</p>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-clock me-1"></i> Segera Hadir
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-dashed">
                    <div class="card-body text-center position-relative">
                        <div class="coming-soon-badge">
                            <span class="badge bg-warning text-dark">Coming Soon</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-users-cog fa-3x text-secondary opacity-50"></i>
                        </div>
                        <h5 class="card-title text-muted">Pengguna & Izin</h5>
                        <p class="card-text text-muted">Kelola pengguna, peran, dan izin akses ke panel admin.</p>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-clock me-1"></i> Segera Hadir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed {
        border: 2px dashed #ddd !important;
        background-color: #f9f9f9;
    }

    .coming-soon-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .opacity-50 {
        opacity: 0.5;
    }
</style>
@endsection



