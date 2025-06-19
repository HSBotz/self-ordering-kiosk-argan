@extends('admin.layouts.app')

@section('title', 'Informasi Toko')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Informasi Toko</span>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Debug Info -->
        <div class="alert alert-info mb-3">
            <h6><i class="fas fa-info-circle"></i> Debug Info</h6>
            <div style="font-size: 12px; max-height: 150px; overflow-y: auto;">
                <pre>{{ json_encode($settings ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <form action="{{ route('admin.settings.store.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Informasi Toko di Frontend</h5>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="store_name" class="form-label">Nama Toko <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'Kedai Coffee') }}" required>
                    <div class="form-text">Nama yang akan ditampilkan di navbar dan di website (mis: "Kedai Coffee").</div>
                    @error('store_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="store_header_name" class="form-label">Nama Toko di Header <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('store_header_name') is-invalid @enderror" id="store_header_name" name="store_header_name" value="{{ old('store_header_name', $settings['store_header_name'] ?? 'Kedai Coffee Kiosk') }}" required>
                    <div class="form-text">Nama yang akan ditampilkan di header halaman utama (mis: "Kedai Coffee Kiosk").</div>
                    @error('store_header_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="store_description" class="form-label">Deskripsi Toko <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('store_description') is-invalid @enderror" id="store_description" name="store_description" rows="3" required>{{ old('store_description', $settings['store_description'] ?? 'Nikmati berbagai pilihan kopi premium dan makanan lezat. Pesan dengan mudah melalui kiosk kami.') }}</textarea>
                    <div class="form-text">Deskripsi yang akan tampil di bawah judul pada halaman utama.</div>
                    @error('store_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="store_tagline" class="form-label">Tagline <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('store_tagline') is-invalid @enderror" id="store_tagline" name="store_tagline" value="{{ old('store_tagline', $settings['store_tagline'] ?? 'Sajian kopi pilihan berkualitas') }}" required>
                    <div class="form-text">Tagline pendek yang akan ditampilkan di title browser.</div>
                    @error('store_tagline')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Pengaturan Halaman Success -->
                <div class="col-md-12 mt-4">
                    <h5 class="border-bottom pb-2">Pengaturan Halaman Sukses</h5>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="show_qr_code" class="form-label">QR Code Tracking</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_qr_code" name="show_qr_code" value="1" {{ (isset($settings['show_qr_code']) && $settings['show_qr_code'] == '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_qr_code">Aktifkan QR Code pada halaman sukses</label>
                    </div>
                    <div class="form-text">Jika diaktifkan, QR code untuk tracking pesanan akan ditampilkan di halaman sukses.</div>
                    @error('show_qr_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="qr_code_size" class="form-label">Ukuran QR Code</label>
                    <select class="form-select @error('qr_code_size') is-invalid @enderror" id="qr_code_size" name="qr_code_size">
                        <option value="small" {{ (isset($settings['qr_code_size']) && $settings['qr_code_size'] == 'small') ? 'selected' : '' }}>Kecil</option>
                        <option value="medium" {{ (!isset($settings['qr_code_size']) || (isset($settings['qr_code_size']) && $settings['qr_code_size'] == 'medium')) ? 'selected' : '' }}>Sedang</option>
                        <option value="large" {{ (isset($settings['qr_code_size']) && $settings['qr_code_size'] == 'large') ? 'selected' : '' }}>Besar</option>
                    </select>
                    <div class="form-text">Ukuran QR code yang akan ditampilkan pada halaman sukses.</div>
                    @error('qr_code_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label">Logo Toko Bawaan</label>
                    <div class="row row-cols-2 row-cols-md-4 g-3 mb-3 logo-options">
                        <!-- Logo option 1 -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-coffee fa-3x text-brown"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_1" value="icon_coffee" {{ isset($settings['store_logo']) && $settings['store_logo'] == 'icon_coffee' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_1">
                                            Coffee Icon
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo option 2 -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-mug-hot fa-3x text-brown"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_2" value="icon_mug" {{ isset($settings['store_logo']) && $settings['store_logo'] == 'icon_mug' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_2">
                                            Mug Icon
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo option 3 -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-store fa-3x text-brown"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_3" value="icon_store" {{ isset($settings['store_logo']) && $settings['store_logo'] == 'icon_store' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_3">
                                            Store Icon
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo option 4 -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-utensils fa-3x text-brown"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_4" value="icon_utensils" {{ isset($settings['store_logo']) && $settings['store_logo'] == 'icon_utensils' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_4">
                                            Utensils Icon
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo option 5 - Upload sendiri -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-upload fa-3x text-secondary"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_custom" value="custom" {{ (!isset($settings['store_logo']) || !in_array($settings['store_logo'], ['icon_coffee', 'icon_mug', 'icon_store', 'icon_utensils'])) && !empty($settings['store_logo']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_custom">
                                            Upload Custom
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo option 6 - No logo -->
                        <div class="col">
                            <div class="card h-100 logo-option">
                                <div class="card-body d-flex align-items-center justify-content-center p-3" style="min-height: 120px;">
                                    <i class="fas fa-times-circle fa-3x text-danger"></i>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="logo_option" id="logo_option_none" value="none" {{ (!isset($settings['store_logo']) || empty($settings['store_logo'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="logo_option_none">
                                            Tanpa Logo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-text mb-3">Pilih salah satu icon yang tersedia atau unggah file gambar custom.</div>
                </div>

                <div class="col-md-12 mb-3 custom-logo-upload" style="{{ (!isset($settings['store_logo']) || !in_array($settings['store_logo'], ['icon_coffee', 'icon_mug', 'icon_store', 'icon_utensils'])) && !empty($settings['store_logo']) ? '' : 'display: none;' }}">
                    <label for="store_logo" class="form-label">Upload Logo Kustom</label>
                    <div class="input-group mb-2">
                        <input type="file" class="form-control @error('store_logo') is-invalid @enderror" id="store_logo" name="store_logo" accept="image/*">
                        <label class="input-group-text" for="store_logo"><i class="fas fa-upload"></i></label>
                    </div>
                    <div class="form-text">Format: JPG, PNG. Maks: 2MB. Ukuran optimal 200x80px.</div>
                    @error('store_logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Pengaturan Admin Dashboard -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Pengaturan Admin Dashboard</h5>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="admin_sidebar_title" class="form-label">Judul Sidebar Admin</label>
                    <input type="text" class="form-control @error('admin_sidebar_title') is-invalid @enderror" id="admin_sidebar_title" name="admin_sidebar_title" value="{{ old('admin_sidebar_title', $settings['admin_sidebar_title'] ?? 'Kedai Coffee Kiosk') }}">
                    <div class="form-text">Teks yang ditampilkan di sidebar admin panel (default: "Kedai Coffee Kiosk").</div>
                    @error('admin_sidebar_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="admin_sidebar_subtitle" class="form-label">Subjudul Sidebar Admin</label>
                    <input type="text" class="form-control @error('admin_sidebar_subtitle') is-invalid @enderror" id="admin_sidebar_subtitle" name="admin_sidebar_subtitle" value="{{ old('admin_sidebar_subtitle', $settings['admin_sidebar_subtitle'] ?? 'Admin Panel') }}">
                    <div class="form-text">Teks kecil di bawah judul sidebar (default: "Admin Panel").</div>
                    @error('admin_sidebar_subtitle')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="store_timezone" class="form-label">Zona Waktu <span class="text-danger">*</span></label>
                    <select class="form-select @error('store_timezone') is-invalid @enderror" id="store_timezone" name="store_timezone" required>
                        <option value="WIB" {{ (isset($settings['store_timezone']) && $settings['store_timezone'] == 'WIB') || !isset($settings['store_timezone']) ? 'selected' : '' }}>WIB (Waktu Indonesia Barat)</option>
                        <option value="WITA" {{ isset($settings['store_timezone']) && $settings['store_timezone'] == 'WITA' ? 'selected' : '' }}>WITA (Waktu Indonesia Tengah)</option>
                        <option value="WIT" {{ isset($settings['store_timezone']) && $settings['store_timezone'] == 'WIT' ? 'selected' : '' }}>WIT (Waktu Indonesia Timur)</option>
                    </select>
                    <div class="form-text">Zona waktu yang digunakan untuk menampilkan tanggal dan waktu di aplikasi.</div>
                    @error('store_timezone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Preview Admin Panel -->
            <div class="card bg-light mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Preview Admin Panel</h6>
                </div>
                <div class="card-body">
                    <div class="sidebar-preview bg-dark text-white p-3 rounded">
                        <div class="p-3 border-bottom border-secondary">
                            <h5 class="mb-0" id="sidebar-title-preview">{{ $settings['admin_sidebar_title'] ?? 'Kedai Coffee Kiosk' }}</h5>
                            <div class="text-muted small" id="sidebar-subtitle-preview">{{ $settings['admin_sidebar_subtitle'] ?? 'Admin Panel' }}</div>
                        </div>
                        <div class="p-2 mt-2">
                            <div class="nav-item-preview bg-secondary bg-opacity-10 p-2 rounded mb-2">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </div>
                            <div class="nav-item-preview p-2 rounded mb-2">
                                <i class="fas fa-mug-hot me-2"></i> Produk
                            </div>
                            <div class="nav-item-preview p-2 rounded mb-2">
                                <i class="fas fa-tags me-2"></i> Kategori
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Pengaturan ini akan mengubah tampilan nama dan icon di panel admin.
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dapatkan semua radio button logo
        const logoOptions = document.querySelectorAll('input[name="logo_option"]');
        const customLogoUpload = document.querySelector('.custom-logo-upload');

        // Tambahkan event listener untuk setiap radio button
        logoOptions.forEach(option => {
            option.addEventListener('change', function() {
                // Tampilkan/sembunyikan upload form berdasarkan pilihan
                if (this.value === 'custom') {
                    customLogoUpload.style.display = 'block';
                } else {
                    customLogoUpload.style.display = 'none';
                }
            });
        });

        // Tambahkan event listener untuk judul sidebar
        const adminSidebarTitle = document.getElementById('admin_sidebar_title');
        const adminSidebarSubtitle = document.getElementById('admin_sidebar_subtitle');
        const sidebarTitlePreview = document.getElementById('sidebar-title-preview');
        const sidebarSubtitlePreview = document.getElementById('sidebar-subtitle-preview');

        adminSidebarTitle.addEventListener('input', function() {
            sidebarTitlePreview.textContent = this.value || 'Kedai Coffee Kiosk';
        });

        adminSidebarSubtitle.addEventListener('input', function() {
            sidebarSubtitlePreview.textContent = this.value || 'Admin Panel';
        });
    });
</script>

<style>
    .logo-option {
        cursor: pointer;
        transition: all 0.2s;
    }
    .logo-option:hover {
        border-color: var(--primary-color);
    }
    .text-brown {
        color: #6a3412;
    }
    .sidebar-preview {
        max-width: 250px;
    }
    .nav-item-preview {
        cursor: pointer;
        transition: all 0.3s;
    }
    .nav-item-preview:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endsection
