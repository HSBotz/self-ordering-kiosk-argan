@extends('admin.layouts.app')

@section('title', 'Edit Produk')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    .image-preview-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 15px;
    }

    .image-preview {
        width: 150px;
        height: 150px;
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

    .cropper-container {
        margin: 0 auto;
    }

    .crop-btn {
        margin-top: 10px;
    }

    #crop-image-modal .modal-body {
        text-align: center;
        padding: 0.5rem;
    }

    #cropper-img-container {
        max-height: 400px;
        margin: 0 auto;
    }

    .image-editor-controls {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .slider-container {
        margin: 10px 0;
    }

    .slider-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
    }

    .filter-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .filter-option {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }

    .filter-option.active {
        border-color: #0d6efd;
    }

    .filter-option img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .filter-name {
        font-size: 0.65rem;
        text-align: center;
        margin-top: 2px;
    }

    .btn-group .btn {
        padding: 0.3rem 0.5rem;
        font-size: 0.75rem;
    }

    .aspect-ratio-option {
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8rem;
        margin: 0 3px;
    }

    .aspect-ratio-option.active {
        background-color: #0d6efd;
        color: white;
    }

    .editor-tab-content {
        padding: 10px 0;
    }

    .editor-tabs {
        margin-bottom: 15px;
    }

    .btn-crop-apply {
        background-color: #6a3412;
        border-color: #6a3412;
    }

    .btn-crop-apply:hover {
        background-color: #52280e;
        border-color: #52280e;
    }

    .image-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    #quality-value, #rotation-value, #brightness-value, #contrast-value, #saturation-value {
        font-weight: bold;
        min-width: 40px;
        display: inline-block;
        text-align: right;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Edit Produk</span>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="product-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">Produk Tersedia</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Produk</label>
                        <div class="image-preview-container">
                            <div class="image-preview" id="image-preview">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <i class="fas fa-image"></i>
                                @endif
                            </div>
                            <small class="text-muted">Preview gambar</small>
                        </div>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image-input" accept="image/*">
                        <input type="hidden" name="image_cropped" id="image-cropped">
                        <input type="hidden" name="image" id="image">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: JPG, PNG. Maks: 200MB.</div>

                        @if($product->image)
                        <div class="d-flex mt-2 gap-2">
                            <button type="button" id="edit-image-btn" class="btn btn-sm btn-outline-primary @if(!$product->image) d-none @endif">
                                <i class="fas fa-crop me-1"></i> Edit Gambar
                            </button>
                            <div class="form-check align-self-center ms-2">
                                <input class="form-check-input" type="checkbox" id="delete_image" name="delete_image" value="1">
                                <label class="form-check-label" for="delete_image">
                                    Hapus gambar
                                </label>
                            </div>
                        </div>
                        @else
                        <button type="button" id="edit-image-btn" class="btn btn-sm btn-outline-primary mt-2 d-none">
                            <i class="fas fa-crop me-1"></i> Edit Gambar
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-md-2">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal untuk crop image -->
<div class="modal fade" id="crop-image-modal" tabindex="-1" aria-labelledby="cropImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropImageModalLabel">Editor Gambar Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab navigasi untuk fitur-fitur editor -->
                <ul class="nav nav-tabs editor-tabs" id="editorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="crop-tab" data-bs-toggle="tab" data-bs-target="#crop-tab-pane" type="button" role="tab" aria-controls="crop-tab-pane" aria-selected="true">
                            <i class="fas fa-crop-alt me-1"></i> Crop
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="adjust-tab" data-bs-toggle="tab" data-bs-target="#adjust-tab-pane" type="button" role="tab" aria-controls="adjust-tab-pane" aria-selected="false">
                            <i class="fas fa-sliders-h me-1"></i> Sesuaikan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="filter-tab" data-bs-toggle="tab" data-bs-target="#filter-tab-pane" type="button" role="tab" aria-controls="filter-tab-pane" aria-selected="false">
                            <i class="fas fa-magic me-1"></i> Filter
                        </button>
                    </li>
                </ul>

                <!-- Kontainer untuk gambar yang akan diedit -->
                <div id="cropper-img-container">
                    <img id="cropper-img" src="" alt="Gambar untuk di-edit">
                </div>

                <!-- Tab konten untuk fitur editor -->
                <div class="tab-content" id="editorTabContent">
                    <!-- Tab Crop -->
                    <div class="tab-pane fade show active editor-tab-content" id="crop-tab-pane" role="tabpanel" aria-labelledby="crop-tab" tabindex="0">
                        <div class="image-editor-controls">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Rasio Aspek:</strong>
                                <div>
                                    <span class="aspect-ratio-option active" data-ratio="1">1:1</span>
                                    <span class="aspect-ratio-option" data-ratio="4/3">4:3</span>
                                    <span class="aspect-ratio-option" data-ratio="16/9">16:9</span>
                                    <span class="aspect-ratio-option" data-ratio="NaN">Bebas</span>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-secondary" id="rotate-left" title="Putar kiri">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="rotate-right" title="Putar kanan">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="flip-horizontal" title="Balik horizontal">
                                            <i class="fas fa-arrows-alt-h"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="flip-vertical" title="Balik vertikal">
                                            <i class="fas fa-arrows-alt-v"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-outline-secondary" id="zoom-in" title="Perbesar">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="zoom-out" title="Perkecil">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="move-mode" title="Mode pindah">
                                            <i class="fas fa-arrows-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="crop-mode" title="Mode crop">
                                            <i class="fas fa-crop"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="reset-crop" title="Reset">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="slider-container">
                                    <div class="slider-label">
                                        <span>Rotasi Presisi:</span>
                                        <span id="rotation-value">0°</span>
                                    </div>
                                    <input type="range" class="form-range" id="rotation-slider" min="-45" max="45" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Adjust (Penyesuaian) -->
                    <div class="tab-pane fade editor-tab-content" id="adjust-tab-pane" role="tabpanel" aria-labelledby="adjust-tab" tabindex="0">
                        <div class="image-editor-controls">
                            <div class="slider-container">
                                <div class="slider-label">
                                    <span>Kecerahan:</span>
                                    <span id="brightness-value">0</span>
                                </div>
                                <input type="range" class="form-range" id="brightness-slider" min="-100" max="100" value="0">
                            </div>

                            <div class="slider-container">
                                <div class="slider-label">
                                    <span>Kontras:</span>
                                    <span id="contrast-value">0</span>
                                </div>
                                <input type="range" class="form-range" id="contrast-slider" min="-100" max="100" value="0">
                            </div>

                            <div class="slider-container">
                                <div class="slider-label">
                                    <span>Saturasi:</span>
                                    <span id="saturation-value">0</span>
                                </div>
                                <input type="range" class="form-range" id="saturation-slider" min="-100" max="100" value="0">
                            </div>

                            <div class="slider-container">
                                <div class="slider-label">
                                    <span>Kualitas Gambar:</span>
                                    <span id="quality-value">90%</span>
                                </div>
                                <input type="range" class="form-range" id="quality-slider" min="30" max="100" value="90">
                            </div>

                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="reset-adjustments">
                                    <i class="fas fa-undo me-1"></i> Reset Semua Penyesuaian
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Filter -->
                    <div class="tab-pane fade editor-tab-content" id="filter-tab-pane" role="tabpanel" aria-labelledby="filter-tab" tabindex="0">
                        <div class="image-editor-controls">
                            <strong class="d-block mb-2">Pilih Filter:</strong>
                            <div class="filter-preview" id="filter-options">
                                <div class="filter-option active" data-filter="normal">
                                    <img id="filter-preview-normal" src="" alt="Normal">
                                    <div class="filter-name">Normal</div>
                                </div>
                                <div class="filter-option" data-filter="grayscale">
                                    <img id="filter-preview-grayscale" src="" alt="Grayscale">
                                    <div class="filter-name">Grayscale</div>
                                </div>
                                <div class="filter-option" data-filter="sepia">
                                    <img id="filter-preview-sepia" src="" alt="Sepia">
                                    <div class="filter-name">Sepia</div>
                                </div>
                                <div class="filter-option" data-filter="vintage">
                                    <img id="filter-preview-vintage" src="" alt="Vintage">
                                    <div class="filter-name">Vintage</div>
                                </div>
                                <div class="filter-option" data-filter="warm">
                                    <img id="filter-preview-warm" src="" alt="Warm">
                                    <div class="filter-name">Warm</div>
                                </div>
                                <div class="filter-option" data-filter="cool">
                                    <img id="filter-preview-cool" src="" alt="Cool">
                                    <div class="filter-name">Cool</div>
                                </div>
                                <div class="filter-option" data-filter="sharp">
                                    <img id="filter-preview-sharp" src="" alt="Sharp">
                                    <div class="filter-name">Sharp</div>
                                </div>
                                <div class="filter-option" data-filter="blur">
                                    <img id="filter-preview-blur" src="" alt="Blur">
                                    <div class="filter-name">Blur</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-crop-apply" id="crop-btn">Terapkan Perubahan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;
    let originalFile;
    let currentImageUrl = null;
    let hasNewImage = false;
    let currentFilter = 'normal';
    let imageQuality = 90;
    let brightness = 0;
    let contrast = 0;
    let saturation = 0;
    let filterApplied = false;

    const cropModal = new bootstrap.Modal(document.getElementById('crop-image-modal'));
    const editImageBtn = document.getElementById('edit-image-btn');
    const imagePreview = document.getElementById('image-preview');
    const imageInput = document.getElementById('image-input');
    const imageCropped = document.getElementById('image-cropped');
    const cropBtn = document.getElementById('crop-btn');
    const cropperImg = document.getElementById('cropper-img');

    // Set currentImageUrl jika ada gambar produk
    @if($product->image)
        currentImageUrl = "{{ asset('storage/' . $product->image) }}";
    @endif

    // Fungsi untuk dataURL to Blob
    function dataURLtoBlob(dataURL) {
        const parts = dataURL.split(';base64,');
        const contentType = parts[0].split(':')[1];
        const raw = window.atob(parts[1]);
        const rawLength = raw.length;
        const uInt8Array = new Uint8Array(rawLength);

        for (let i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
    }

    // Fungsi untuk membuat nama file dengan timestamp
    function createFileName(originalName) {
        const extension = originalName.split('.').pop();
        return `product_image_${Date.now()}.${extension}`;
    }

    // Fungsi untuk mempersiapkan filter preview
    function prepareFilterPreviews(imgSrc) {
        // Set gambar preview untuk setiap filter
        document.querySelectorAll('#filter-options .filter-option img').forEach(img => {
            img.src = imgSrc;
        });

        // Terapkan filter untuk setiap preview
        applyFilterToPreview('grayscale', 'grayscale(100%)');
        applyFilterToPreview('sepia', 'sepia(100%)');
        applyFilterToPreview('vintage', 'sepia(50%) contrast(85%) brightness(90%) saturate(85%)');
        applyFilterToPreview('warm', 'saturate(150%) sepia(30%) hue-rotate(20deg)');
        applyFilterToPreview('cool', 'saturate(80%) hue-rotate(180deg)');
        applyFilterToPreview('sharp', 'contrast(150%) brightness(100%)');
        applyFilterToPreview('blur', 'blur(2px)');
    }

    // Fungsi untuk menerapkan filter pada preview
    function applyFilterToPreview(filterId, filterStyle) {
        const previewImg = document.getElementById(`filter-preview-${filterId}`);
        if (previewImg) {
            previewImg.style.filter = filterStyle;
        }
    }

    // Fungsi untuk menerapkan filter pada gambar cropper
    function applyFilterToCropper(filterName) {
        let filterStyle = '';

        switch (filterName) {
            case 'grayscale':
                filterStyle = 'grayscale(100%)';
                break;
            case 'sepia':
                filterStyle = 'sepia(100%)';
                break;
            case 'vintage':
                filterStyle = 'sepia(50%) contrast(85%) brightness(90%) saturate(85%)';
                break;
            case 'warm':
                filterStyle = 'saturate(150%) sepia(30%) hue-rotate(20deg)';
                break;
            case 'cool':
                filterStyle = 'saturate(80%) hue-rotate(180deg)';
                break;
            case 'sharp':
                filterStyle = 'contrast(150%) brightness(100%)';
                break;
            case 'blur':
                filterStyle = 'blur(2px)';
                break;
            default:
                filterStyle = 'none';
                break;
        }

        // Tambahkan penyesuaian kecerahan, kontras, dan saturasi
        if (filterName !== 'normal') {
            filterApplied = true;
        } else {
            filterApplied = false;

            // Jika filter normal, tetap terapkan penyesuaian kecerahan, kontras, dan saturasi
            if (brightness !== 0 || contrast !== 0 || saturation !== 0) {
                filterStyle = '';
            }
        }

        // Tambahkan penyesuaian jika ada
        if (brightness !== 0) {
            filterStyle += ` brightness(${100 + brightness}%)`;
        }
        if (contrast !== 0) {
            filterStyle += ` contrast(${100 + contrast}%)`;
        }
        if (saturation !== 0) {
            filterStyle += ` saturate(${100 + saturation}%)`;
        }

        // Terapkan filter ke gambar cropper
        if (cropperImg.parentElement) {
            cropperImg.parentElement.style.filter = filterStyle;
        }

        currentFilter = filterName;
    }

    // Fungsi untuk edit gambar yang sudah ada
    function editExistingImage() {
        if (currentImageUrl) {
            cropperImg.src = currentImageUrl;
            cropModal.show();

            // Reset filter dan penyesuaian
            currentFilter = 'normal';
            brightness = 0;
            contrast = 0;
            saturation = 0;
            filterApplied = false;

            // Reset slider values
            document.getElementById('brightness-slider').value = 0;
            document.getElementById('contrast-slider').value = 0;
            document.getElementById('saturation-slider').value = 0;
            document.getElementById('rotation-slider').value = 0;
            document.getElementById('quality-slider').value = 90;

            document.getElementById('brightness-value').textContent = '0';
            document.getElementById('contrast-value').textContent = '0';
            document.getElementById('saturation-value').textContent = '0';
            document.getElementById('rotation-value').textContent = '0°';
            document.getElementById('quality-value').textContent = '90%';

            // Inisialisasi cropper setelah modal ditampilkan
            cropModal._element.addEventListener('shown.bs.modal', function() {
                cropper = new Cropper(cropperImg, {
                    aspectRatio: 1, // Rasio 1:1 untuk gambar kotak
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: function() {
                        // Siapkan filter previews setelah cropper siap
                        prepareFilterPreviews(currentImageUrl);
                    }
                });

                // Reset semua filter options
                document.querySelectorAll('.filter-option').forEach(option => {
                    option.classList.remove('active');
                });
                document.querySelector('.filter-option[data-filter="normal"]').classList.add('active');

                // Reset penyesuaian pada tampilan
                applyFilterToCropper('normal');
            }, { once: true });
        }
    }

    // Preview gambar yang dipilih
    imageInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            originalFile = e.target.files[0];
            const reader = new FileReader();
            hasNewImage = true;

            reader.onload = function(e) {
                currentImageUrl = e.target.result;

                // Hapus icon dan tambahkan gambar
                imagePreview.innerHTML = '';
                const img = document.createElement('img');
                img.src = currentImageUrl;
                imagePreview.appendChild(img);

                // Tampilkan tombol edit gambar
                editImageBtn.classList.remove('d-none');

                // Simpan file asli ke input tersembunyi
                const fileInput = document.getElementById('image');

                // Buat file list baru dengan file yang dipilih
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(originalFile);
                fileInput.files = dataTransfer.files;

                // Uncheck delete image checkbox jika ada
                const deleteImageCheckbox = document.getElementById('delete_image');
                if (deleteImageCheckbox) {
                    deleteImageCheckbox.checked = false;
                }
            }

            reader.readAsDataURL(originalFile);
        } else {
            // Kembalikan ke icon default atau gambar yang ada
            if (currentImageUrl && !hasNewImage) {
                imagePreview.innerHTML = `<img src="${currentImageUrl}" alt="Preview">`;
                editImageBtn.classList.remove('d-none');
            } else {
                imagePreview.innerHTML = '<i class="fas fa-image"></i>';
                editImageBtn.classList.add('d-none');
            }
        }
    });

    // Event listener untuk opsi rasio aspek
    document.querySelectorAll('.aspect-ratio-option').forEach(option => {
        option.addEventListener('click', function() {
            // Hapus kelas aktif dari semua opsi
            document.querySelectorAll('.aspect-ratio-option').forEach(o => {
                o.classList.remove('active');
            });

            // Tambahkan kelas aktif ke opsi yang dipilih
            this.classList.add('active');

            // Dapatkan nilai rasio
            const ratio = this.getAttribute('data-ratio');

            // Terapkan rasio ke cropper
            cropper.setAspectRatio(eval(ratio));
        });
    });

    // Event listener untuk slider rotasi presisi
    document.getElementById('rotation-slider').addEventListener('input', function() {
        const value = this.value;
        document.getElementById('rotation-value').textContent = `${value}°`;

        // Terapkan rotasi ke cropper
        cropper.rotateTo(value);
    });

    // Event listener untuk slider kecerahan
    document.getElementById('brightness-slider').addEventListener('input', function() {
        brightness = parseInt(this.value);
        document.getElementById('brightness-value').textContent = brightness;

        // Terapkan ulang filter dengan nilai kecerahan baru
        applyFilterToCropper(currentFilter);
    });

    // Event listener untuk slider kontras
    document.getElementById('contrast-slider').addEventListener('input', function() {
        contrast = parseInt(this.value);
        document.getElementById('contrast-value').textContent = contrast;

        // Terapkan ulang filter dengan nilai kontras baru
        applyFilterToCropper(currentFilter);
    });

    // Event listener untuk slider saturasi
    document.getElementById('saturation-slider').addEventListener('input', function() {
        saturation = parseInt(this.value);
        document.getElementById('saturation-value').textContent = saturation;

        // Terapkan ulang filter dengan nilai saturasi baru
        applyFilterToCropper(currentFilter);
    });

    // Event listener untuk slider kualitas
    document.getElementById('quality-slider').addEventListener('input', function() {
        imageQuality = parseInt(this.value);
        document.getElementById('quality-value').textContent = `${imageQuality}%`;
    });

    // Event listener untuk reset penyesuaian
    document.getElementById('reset-adjustments').addEventListener('click', function() {
        // Reset semua slider
        document.getElementById('brightness-slider').value = 0;
        document.getElementById('contrast-slider').value = 0;
        document.getElementById('saturation-slider').value = 0;

        // Reset nilai teks
        document.getElementById('brightness-value').textContent = '0';
        document.getElementById('contrast-value').textContent = '0';
        document.getElementById('saturation-value').textContent = '0';

        // Reset variabel
        brightness = 0;
        contrast = 0;
        saturation = 0;

        // Terapkan ulang filter
        applyFilterToCropper(currentFilter);
    });

    // Event listener untuk opsi filter
    document.querySelectorAll('.filter-option').forEach(option => {
        option.addEventListener('click', function() {
            // Hapus kelas aktif dari semua opsi
            document.querySelectorAll('.filter-option').forEach(o => {
                o.classList.remove('active');
            });

            // Tambahkan kelas aktif ke opsi yang dipilih
            this.classList.add('active');

            // Dapatkan nilai filter
            const filter = this.getAttribute('data-filter');

            // Terapkan filter
            applyFilterToCropper(filter);
        });
    });

    // Buka modal cropper ketika tombol edit diklik
    editImageBtn.addEventListener('click', function() {
        if (hasNewImage && originalFile) {
            // Jika ada file gambar baru
            const reader = new FileReader();
            reader.onload = function(e) {
                cropperImg.src = e.target.result;
                cropModal.show();

                // Reset filter dan penyesuaian
                currentFilter = 'normal';
                brightness = 0;
                contrast = 0;
                saturation = 0;
                filterApplied = false;

                // Reset slider values
                document.getElementById('brightness-slider').value = 0;
                document.getElementById('contrast-slider').value = 0;
                document.getElementById('saturation-slider').value = 0;
                document.getElementById('rotation-slider').value = 0;
                document.getElementById('quality-slider').value = 90;

                document.getElementById('brightness-value').textContent = '0';
                document.getElementById('contrast-value').textContent = '0';
                document.getElementById('saturation-value').textContent = '0';
                document.getElementById('rotation-value').textContent = '0°';
                document.getElementById('quality-value').textContent = '90%';

                // Inisialisasi cropper setelah modal ditampilkan
                cropModal._element.addEventListener('shown.bs.modal', function() {
                    cropper = new Cropper(cropperImg, {
                        aspectRatio: 1, // Default rasio 1:1
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        ready: function() {
                            // Siapkan filter previews setelah cropper siap
                            prepareFilterPreviews(e.target.result);
                        }
                    });

                    // Reset semua filter options
                    document.querySelectorAll('.filter-option').forEach(option => {
                        option.classList.remove('active');
                    });
                    document.querySelector('.filter-option[data-filter="normal"]').classList.add('active');

                    // Reset penyesuaian pada tampilan
                    applyFilterToCropper('normal');
                }, { once: true });
            }

            reader.readAsDataURL(originalFile);
        } else if (currentImageUrl) {
            // Edit gambar yang sudah ada
            editExistingImage();
        }
    });

    // Toggle mode drag
    document.getElementById('move-mode').addEventListener('click', function() {
        cropper.setDragMode('move');
    });

    // Toggle mode crop
    document.getElementById('crop-mode').addEventListener('click', function() {
        cropper.setDragMode('crop');
    });

    // Jika kotak centang "Hapus gambar" dicentang
    const deleteImageCheckbox = document.getElementById('delete_image');
    if (deleteImageCheckbox) {
        deleteImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Jika kotak centang dicentang, tampilkan ikon default
                imagePreview.innerHTML = '<i class="fas fa-image"></i>';
                editImageBtn.classList.add('d-none');
                hasNewImage = false;

                // Hapus nilai pada input file tersembunyi
                const fileInput = document.getElementById('image');
                fileInput.value = '';
            } else {
                // Jika kotak centang tidak dicentang, kembalikan gambar saat ini
                if (currentImageUrl && !hasNewImage) {
                    imagePreview.innerHTML = `<img src="${currentImageUrl}" alt="{{ $product->name }}">`;
                    editImageBtn.classList.remove('d-none');
                }
            }
        });
    }

    // Tombol kontrol cropper
    document.getElementById('rotate-left').addEventListener('click', function() {
        cropper.rotate(-90);
        // Reset rotasi presisi
        document.getElementById('rotation-slider').value = 0;
        document.getElementById('rotation-value').textContent = '0°';
    });

    document.getElementById('rotate-right').addEventListener('click', function() {
        cropper.rotate(90);
        // Reset rotasi presisi
        document.getElementById('rotation-slider').value = 0;
        document.getElementById('rotation-value').textContent = '0°';
    });

    document.getElementById('flip-horizontal').addEventListener('click', function() {
        cropper.scaleX(cropper.getData().scaleX === 1 ? -1 : 1);
    });

    document.getElementById('flip-vertical').addEventListener('click', function() {
        cropper.scaleY(cropper.getData().scaleY === 1 ? -1 : 1);
    });

    document.getElementById('zoom-in').addEventListener('click', function() {
        cropper.zoom(0.1);
    });

    document.getElementById('zoom-out').addEventListener('click', function() {
        cropper.zoom(-0.1);
    });

    document.getElementById('reset-crop').addEventListener('click', function() {
        cropper.reset();
        // Reset rotasi presisi
        document.getElementById('rotation-slider').value = 0;
        document.getElementById('rotation-value').textContent = '0°';
    });

    // Terapkan perubahan crop
    cropBtn.addEventListener('click', function() {
        // Dapatkan canvas hasil crop
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
            minWidth: 256,
            minHeight: 256,
            maxWidth: 4096,
            maxHeight: 4096,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Buat canvas baru untuk menerapkan filter jika ada
        if (filterApplied || brightness !== 0 || contrast !== 0 || saturation !== 0) {
            // Buat elemen canvas baru
            const filteredCanvas = document.createElement('canvas');
            const ctx = filteredCanvas.getContext('2d');

            // Setel ukuran canvas
            filteredCanvas.width = canvas.width;
            filteredCanvas.height = canvas.height;

            // Gambar gambar asli ke canvas baru
            ctx.filter = cropperImg.parentElement.style.filter;
            ctx.drawImage(canvas, 0, 0, canvas.width, canvas.height);

            // Gunakan canvas dengan filter
            canvas.getContext('2d').drawImage(filteredCanvas, 0, 0);
        }

        // Konversi canvas ke data URL dengan kualitas yang disesuaikan
        const croppedImageUrl = canvas.toDataURL('image/jpeg', imageQuality / 100);

        // Update preview
        imagePreview.innerHTML = '';
        const img = document.createElement('img');
        img.src = croppedImageUrl;
        imagePreview.appendChild(img);

        // Simpan gambar yang sudah di-crop ke input tersembunyi
        imageCropped.value = croppedImageUrl;

        // Buat file baru dari data URL
        const blob = dataURLtoBlob(croppedImageUrl);
        const filename = hasNewImage && originalFile ? createFileName(originalFile.name) : createFileName('image.jpg');
        const croppedFile = new File([blob], filename, { type: 'image/jpeg' });

        // Perbarui file input tersembunyi
        const fileInput = document.getElementById('image');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(croppedFile);
        fileInput.files = dataTransfer.files;

        // Tutup modal
        cropModal.hide();

        // Destroy cropper instance
        cropper.destroy();
    });

    // Destroy cropper saat modal ditutup
    document.getElementById('crop-image-modal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
</script>
@endsection
