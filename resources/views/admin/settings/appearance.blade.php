@extends('admin.layouts.app')

@section('title', 'Pengaturan Tampilan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Pengaturan Tampilan</span>
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

        <form action="{{ route('admin.settings.appearance.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Pengaturan Warna</h5>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_primary_color" class="form-label">Warna Utama <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <input type="color" class="form-control form-control-color me-2" id="appearance_primary_color" name="appearance_primary_color" value="{{ old('appearance_primary_color', $settings['appearance_primary_color'] ?? '#6a3412') }}" title="Pilih warna utama">
                        <input type="text" class="form-control w-50 @error('appearance_primary_color') is-invalid @enderror" value="{{ old('appearance_primary_color', $settings['appearance_primary_color'] ?? '#6a3412') }}" aria-label="Kode warna" id="appearance_primary_color_text">
                    </div>
                    <div class="form-text">Digunakan untuk navbar, button, dan elemen utama.</div>
                    @error('appearance_primary_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_secondary_color" class="form-label">Warna Sekunder <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <input type="color" class="form-control form-control-color me-2" id="appearance_secondary_color" name="appearance_secondary_color" value="{{ old('appearance_secondary_color', $settings['appearance_secondary_color'] ?? '#a87d56') }}" title="Pilih warna sekunder">
                        <input type="text" class="form-control w-50 @error('appearance_secondary_color') is-invalid @enderror" value="{{ old('appearance_secondary_color', $settings['appearance_secondary_color'] ?? '#a87d56') }}" aria-label="Kode warna" id="appearance_secondary_color_text">
                    </div>
                    <div class="form-text">Digunakan untuk elemen pendukung dan latar belakang.</div>
                    @error('appearance_secondary_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_accent_color" class="form-label">Warna Aksen <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <input type="color" class="form-control form-control-color me-2" id="appearance_accent_color" name="appearance_accent_color" value="{{ old('appearance_accent_color', $settings['appearance_accent_color'] ?? '#ffb74d') }}" title="Pilih warna aksen">
                        <input type="text" class="form-control w-50 @error('appearance_accent_color') is-invalid @enderror" value="{{ old('appearance_accent_color', $settings['appearance_accent_color'] ?? '#ffb74d') }}" aria-label="Kode warna" id="appearance_accent_color_text">
                    </div>
                    <div class="form-text">Digunakan untuk highlight, badge, dan elemen yang perlu ditonjolkan.</div>
                    @error('appearance_accent_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_text_color" class="form-label">Warna Teks <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <input type="color" class="form-control form-control-color me-2" id="appearance_text_color" name="appearance_text_color" value="{{ old('appearance_text_color', $settings['appearance_text_color'] ?? '#333333') }}" title="Pilih warna teks">
                        <input type="text" class="form-control w-50 @error('appearance_text_color') is-invalid @enderror" value="{{ old('appearance_text_color', $settings['appearance_text_color'] ?? '#333333') }}" aria-label="Kode warna" id="appearance_text_color_text">
                    </div>
                    <div class="form-text">Warna teks utama di seluruh website.</div>
                    @error('appearance_text_color')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Pengaturan Font</h5>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_heading_font" class="form-label">Font Heading <span class="text-danger">*</span></label>
                    <select class="form-select @error('appearance_heading_font') is-invalid @enderror" id="appearance_heading_font" name="appearance_heading_font" required>
                        <option value="Poppins" {{ (old('appearance_heading_font', $settings['appearance_heading_font'] ?? '') === 'Poppins') ? 'selected' : '' }}>Poppins</option>
                        <option value="Roboto" {{ (old('appearance_heading_font', $settings['appearance_heading_font'] ?? '') === 'Roboto') ? 'selected' : '' }}>Roboto</option>
                        <option value="Montserrat" {{ (old('appearance_heading_font', $settings['appearance_heading_font'] ?? '') === 'Montserrat') ? 'selected' : '' }}>Montserrat</option>
                        <option value="Open Sans" {{ (old('appearance_heading_font', $settings['appearance_heading_font'] ?? '') === 'Open Sans') ? 'selected' : '' }}>Open Sans</option>
                        <option value="Lato" {{ (old('appearance_heading_font', $settings['appearance_heading_font'] ?? '') === 'Lato') ? 'selected' : '' }}>Lato</option>
                    </select>
                    <div class="form-text">Font untuk judul dan heading.</div>
                    @error('appearance_heading_font')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="appearance_body_font" class="form-label">Font Body <span class="text-danger">*</span></label>
                    <select class="form-select @error('appearance_body_font') is-invalid @enderror" id="appearance_body_font" name="appearance_body_font" required>
                        <option value="Poppins" {{ (old('appearance_body_font', $settings['appearance_body_font'] ?? '') === 'Poppins') ? 'selected' : '' }}>Poppins</option>
                        <option value="Roboto" {{ (old('appearance_body_font', $settings['appearance_body_font'] ?? '') === 'Roboto') ? 'selected' : '' }}>Roboto</option>
                        <option value="Montserrat" {{ (old('appearance_body_font', $settings['appearance_body_font'] ?? '') === 'Montserrat') ? 'selected' : '' }}>Montserrat</option>
                        <option value="Open Sans" {{ (old('appearance_body_font', $settings['appearance_body_font'] ?? '') === 'Open Sans') ? 'selected' : '' }}>Open Sans</option>
                        <option value="Lato" {{ (old('appearance_body_font', $settings['appearance_body_font'] ?? '') === 'Lato') ? 'selected' : '' }}>Lato</option>
                    </select>
                    <div class="form-text">Font untuk teks konten utama.</div>
                    @error('appearance_body_font')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Pratinjau</h5>
                    <div class="p-3 border rounded mt-3 mb-3" id="preview">
                        <h3>Ini adalah tampilan heading</h3>
                        <p>Ini adalah contoh teks paragraf yang akan tampak di website Anda. Perubahan warna dan font akan terlihat di sini.</p>
                        <button class="btn btn-primary mt-2">Tombol Utama</button>
                        <button class="btn btn-outline-secondary mt-2 ms-2">Tombol Sekunder</button>
                        <span class="badge rounded-pill ms-2" style="background-color: var(--accent-color); color: var(--primary-color);">Badge</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Sync color inputs
    function syncColorInputs(colorInput, textInput) {
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
            updatePreview();
        });

        textInput.addEventListener('input', function() {
            colorInput.value = this.value;
            updatePreview();
        });
    }

    // Update preview based on selected values
    function updatePreview() {
        const primaryColor = document.getElementById('appearance_primary_color').value;
        const secondaryColor = document.getElementById('appearance_secondary_color').value;
        const accentColor = document.getElementById('appearance_accent_color').value;
        const textColor = document.getElementById('appearance_text_color').value;
        const headingFont = document.getElementById('appearance_heading_font').value;
        const bodyFont = document.getElementById('appearance_body_font').value;

        const preview = document.getElementById('preview');
        preview.style.setProperty('--primary-color', primaryColor);
        preview.style.setProperty('--secondary-color', secondaryColor);
        preview.style.setProperty('--accent-color', accentColor);
        preview.style.setProperty('--text-color', textColor);

        // Update fonts
        document.querySelectorAll('#preview h3').forEach(h => {
            h.style.fontFamily = headingFont + ', sans-serif';
        });

        document.querySelectorAll('#preview p').forEach(p => {
            p.style.fontFamily = bodyFont + ', sans-serif';
        });

        // Update button styles
        document.querySelectorAll('#preview .btn-primary').forEach(btn => {
            btn.style.backgroundColor = primaryColor;
            btn.style.borderColor = primaryColor;
            btn.style.color = '#fff';
        });

        document.querySelectorAll('#preview .btn-outline-secondary').forEach(btn => {
            btn.style.color = secondaryColor;
            btn.style.borderColor = secondaryColor;
        });
    }

    // Initialize on document ready
    document.addEventListener('DOMContentLoaded', function() {
        // Sync color inputs with text inputs
        syncColorInputs(
            document.getElementById('appearance_primary_color'),
            document.getElementById('appearance_primary_color_text')
        );
        syncColorInputs(
            document.getElementById('appearance_secondary_color'),
            document.getElementById('appearance_secondary_color_text')
        );
        syncColorInputs(
            document.getElementById('appearance_accent_color'),
            document.getElementById('appearance_accent_color_text')
        );
        syncColorInputs(
            document.getElementById('appearance_text_color'),
            document.getElementById('appearance_text_color_text')
        );

        // Listen for font changes
        document.getElementById('appearance_heading_font').addEventListener('change', updatePreview);
        document.getElementById('appearance_body_font').addEventListener('change', updatePreview);

        // Initial preview
        updatePreview();
    });
</script>
@endsection
@endsection
