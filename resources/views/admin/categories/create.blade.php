@extends('admin.layouts.app')

@section('title', 'Tambah Kategori Baru')

@section('styles')
<style>
    .icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 15px;
        max-height: 300px;
        overflow-y: auto;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .icon-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .icon-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-3px);
    }

    .icon-item.selected {
        background-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(106, 52, 18, 0.3);
    }

    .icon-item i {
        font-size: 24px;
        margin-bottom: 5px;
    }

    .icon-item .icon-name {
        font-size: 9px;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        padding: 0 2px;
    }

    .image-preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 15px;
    }

    .image-preview {
        width: 120px;
        height: 120px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-color);
        margin-bottom: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .image-preview i {
        font-size: 48px;
        color: var(--primary-color);
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tabs-container {
        margin-bottom: 15px;
    }

    .nav-tabs .nav-link {
        border-radius: 10px 10px 0 0;
        padding: 10px 20px;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 10px 10px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Tambah Kategori Baru</span>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="media_type" id="media-type" value="icon">

            <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="has_variants" name="has_variants" value="1" {{ old('has_variants') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_variants">Aktifkan Pilihan Panas/Dingin</label>
                </div>
                <div class="form-text">Aktifkan opsi ini untuk kategori minuman yang dapat disajikan panas atau dingin.</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Gambar/Icon Kategori</label>

                <div class="tabs-container">
                    <ul class="nav nav-tabs" id="imageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="icon-tab" data-bs-toggle="tab" data-bs-target="#icon-tab-pane" type="button" role="tab" aria-controls="icon-tab-pane" aria-selected="true">
                                <i class="fas fa-icons me-1"></i> Pilih Icon
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-tab-pane" type="button" role="tab" aria-controls="upload-tab-pane" aria-selected="false">
                                <i class="fas fa-upload me-1"></i> Upload Gambar
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="imageTabsContent">
                        <div class="tab-pane fade show active" id="icon-tab-pane" role="tabpanel" aria-labelledby="icon-tab" tabindex="0">
                            <div class="image-preview-container">
                                <div class="image-preview" id="selected-icon-preview">
                                    <i class="fas fa-coffee"></i>
                                </div>
                                <small class="text-muted">Icon terpilih</small>
                            </div>

                            <input type="hidden" name="icon" id="selected-icon-input" value="fas fa-coffee">

                            <div class="icon-grid">
                                <!-- Food & Drink Icons -->
                                <div class="icon-item selected" data-icon="fas fa-coffee">
                                    <i class="fas fa-coffee"></i>
                                    <span class="icon-name">Coffee</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-mug-hot">
                                    <i class="fas fa-mug-hot"></i>
                                    <span class="icon-name">Hot Drink</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-glass-martini-alt">
                                    <i class="fas fa-glass-martini-alt"></i>
                                    <span class="icon-name">Cocktail</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-beer">
                                    <i class="fas fa-beer"></i>
                                    <span class="icon-name">Beer</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-wine-glass-alt">
                                    <i class="fas fa-wine-glass-alt"></i>
                                    <span class="icon-name">Wine</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-cookie">
                                    <i class="fas fa-cookie"></i>
                                    <span class="icon-name">Cookie</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-ice-cream">
                                    <i class="fas fa-ice-cream"></i>
                                    <span class="icon-name">Ice Cream</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-pizza-slice">
                                    <i class="fas fa-pizza-slice"></i>
                                    <span class="icon-name">Pizza</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-hamburger">
                                    <i class="fas fa-hamburger"></i>
                                    <span class="icon-name">Burger</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-hotdog">
                                    <i class="fas fa-hotdog"></i>
                                    <span class="icon-name">Hotdog</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-bacon">
                                    <i class="fas fa-bacon"></i>
                                    <span class="icon-name">Bacon</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-drumstick-bite">
                                    <i class="fas fa-drumstick-bite"></i>
                                    <span class="icon-name">Chicken</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-carrot">
                                    <i class="fas fa-carrot"></i>
                                    <span class="icon-name">Vegetable</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-leaf">
                                    <i class="fas fa-leaf"></i>
                                    <span class="icon-name">Vegan</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-pepper-hot">
                                    <i class="fas fa-pepper-hot"></i>
                                    <span class="icon-name">Spicy</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-egg">
                                    <i class="fas fa-egg"></i>
                                    <span class="icon-name">Egg</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-apple-alt">
                                    <i class="fas fa-apple-alt"></i>
                                    <span class="icon-name">Fruit</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-cheese">
                                    <i class="fas fa-cheese"></i>
                                    <span class="icon-name">Cheese</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-bread-slice">
                                    <i class="fas fa-bread-slice"></i>
                                    <span class="icon-name">Bread</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-utensils">
                                    <i class="fas fa-utensils"></i>
                                    <span class="icon-name">Food</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-birthday-cake">
                                    <i class="fas fa-birthday-cake"></i>
                                    <span class="icon-name">Cake</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-candy-cane">
                                    <i class="fas fa-candy-cane"></i>
                                    <span class="icon-name">Candy</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-fish">
                                    <i class="fas fa-fish"></i>
                                    <span class="icon-name">Seafood</span>
                                </div>
                                <div class="icon-item" data-icon="fas fa-seedling">
                                    <i class="fas fa-seedling"></i>
                                    <span class="icon-name">Organic</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="upload-tab-pane" role="tabpanel" aria-labelledby="upload-tab" tabindex="0">
                            <div class="image-preview-container">
                                <div class="image-preview" id="image-preview">
                                    <i class="fas fa-image"></i>
                                </div>
                                <small class="text-muted">Gambar terpilih</small>
                            </div>

                            <div class="mb-3">
                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                                <small class="form-text text-muted">Upload gambar dengan format JPG, PNG, atau JPEG. Maksimal 200MB.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="is_active" class="form-label">Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Aktif
                    </label>
                </div>
                <small class="form-text text-muted">Kategori yang tidak aktif tidak akan ditampilkan di menu kiosk.</small>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi untuk icon picker
    document.querySelectorAll('.icon-item').forEach(item => {
        item.addEventListener('click', function() {
            // Hapus class selected dari semua icon
            document.querySelectorAll('.icon-item').forEach(i => i.classList.remove('selected'));

            // Tambahkan class selected ke icon yang dipilih
            this.classList.add('selected');

            // Set icon yang dipilih
            const selectedIcon = this.getAttribute('data-icon');
            document.getElementById('selected-icon-input').value = selectedIcon;

            // Update preview
            const previewContainer = document.getElementById('selected-icon-preview');
            previewContainer.innerHTML = `<i class="${selectedIcon}"></i>`;

            // Aktifkan tab icon
            const iconTab = document.getElementById('icon-tab');
            bootstrap.Tab.getOrCreateInstance(iconTab).show();
        });
    });

    // Preview gambar yang diupload
    document.getElementById('image').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('image-preview');

            previewContainer.innerHTML = '';
            const preview = document.createElement('img');
            preview.id = 'preview';
            preview.src = e.target.result;
            previewContainer.appendChild(preview);

            // Aktifkan tab upload dan ubah tipe media
            const uploadTab = document.getElementById('upload-tab');
            bootstrap.Tab.getOrCreateInstance(uploadTab).show();
            document.getElementById('media-type').value = 'image';
        }

        if (e.target.files[0]) {
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Toggle antara icon dan gambar
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            // Jika tab gambar aktif, atur tipe media ke 'image'
            if (e.target.id === 'upload-tab') {
                document.getElementById('media-type').value = 'image';
            }
            // Jika tab icon aktif, atur tipe media ke 'icon'
            else if (e.target.id === 'icon-tab') {
                document.getElementById('media-type').value = 'icon';
            }
        });
    });
</script>
@endsection
