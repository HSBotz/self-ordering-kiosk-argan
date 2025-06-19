@extends('admin.layouts.app')

@section('title', 'Pengaturan Footer')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Pengaturan Footer</span>
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

        <form action="{{ route('admin.settings.footer.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Kolom Tentang</h5>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="footer_about_title" class="form-label">Judul</label>
                    <input type="text" class="form-control @error('footer_about_title') is-invalid @enderror" id="footer_about_title" name="footer_about_title" value="{{ old('footer_about_title', $settings['footer_about_title'] ?? '') }}">
                    @error('footer_about_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="footer_about_text" class="form-label">Teks</label>
                    <textarea class="form-control @error('footer_about_text') is-invalid @enderror" id="footer_about_text" name="footer_about_text" rows="3">{{ old('footer_about_text', $settings['footer_about_text'] ?? '') }}</textarea>
                    @error('footer_about_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Media Sosial</h5>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card p-3 bg-light">
                        <h6 class="mb-2">Visibilitas Media Sosial</h6>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="footer_social_media_visible_1" name="footer_social_media_visible" value="1" {{ (!isset($settings['footer_social_media_visible']) || $settings['footer_social_media_visible'] == '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="footer_social_media_visible_1">
                                    <span class="text-success"><i class="fas fa-eye me-1"></i> Tampilkan</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="footer_social_media_visible_0" name="footer_social_media_visible" value="0" {{ (isset($settings['footer_social_media_visible']) && $settings['footer_social_media_visible'] == '0') ? 'checked' : '' }}>
                                <label class="form-check-label" for="footer_social_media_visible_0">
                                    <span class="text-danger"><i class="fas fa-eye-slash me-1"></i> Sembunyikan</span>
                                </label>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">Pilih "Sembunyikan" jika Anda ingin menyembunyikan ikon media sosial di footer</small>
                        @if(isset($settings['footer_social_media_visible']))
                        <div class="mt-2">
                            <small class="text-info">
                                <i class="fas fa-info-circle"></i>
                                Nilai saat ini: {{ $settings['footer_social_media_visible'] == '1' ? 'Tampilkan' : 'Sembunyikan' }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_social_facebook" class="form-label">Facebook URL</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                        <input type="text" class="form-control @error('footer_social_facebook') is-invalid @enderror" id="footer_social_facebook" name="footer_social_facebook" value="{{ old('footer_social_facebook', $settings['footer_social_facebook'] ?? '') }}">
                    </div>
                    @error('footer_social_facebook')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_social_instagram" class="form-label">Instagram URL</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                        <input type="text" class="form-control @error('footer_social_instagram') is-invalid @enderror" id="footer_social_instagram" name="footer_social_instagram" value="{{ old('footer_social_instagram', $settings['footer_social_instagram'] ?? '') }}">
                    </div>
                    @error('footer_social_instagram')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_social_twitter" class="form-label">Twitter URL</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                        <input type="text" class="form-control @error('footer_social_twitter') is-invalid @enderror" id="footer_social_twitter" name="footer_social_twitter" value="{{ old('footer_social_twitter', $settings['footer_social_twitter'] ?? '') }}">
                    </div>
                    @error('footer_social_twitter')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Jam Buka</h5>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_hours_title" class="form-label">Judul</label>
                    <input type="text" class="form-control @error('footer_hours_title') is-invalid @enderror" id="footer_hours_title" name="footer_hours_title" value="{{ old('footer_hours_title', $settings['footer_hours_title'] ?? '') }}">
                    @error('footer_hours_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_hours_weekday" class="form-label">Jam Kerja (Senin-Jumat)</label>
                    <input type="text" class="form-control @error('footer_hours_weekday') is-invalid @enderror" id="footer_hours_weekday" name="footer_hours_weekday" value="{{ old('footer_hours_weekday', $settings['footer_hours_weekday'] ?? '') }}">
                    @error('footer_hours_weekday')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_hours_weekend" class="form-label">Jam Kerja (Sabtu-Minggu)</label>
                    <input type="text" class="form-control @error('footer_hours_weekend') is-invalid @enderror" id="footer_hours_weekend" name="footer_hours_weekend" value="{{ old('footer_hours_weekend', $settings['footer_hours_weekend'] ?? '') }}">
                    @error('footer_hours_weekend')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Kontak</h5>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="footer_contact_title" class="form-label">Judul</label>
                    <input type="text" class="form-control @error('footer_contact_title') is-invalid @enderror" id="footer_contact_title" name="footer_contact_title" value="{{ old('footer_contact_title', $settings['footer_contact_title'] ?? '') }}">
                    @error('footer_contact_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_contact_address" class="form-label">Alamat</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" class="form-control @error('footer_contact_address') is-invalid @enderror" id="footer_contact_address" name="footer_contact_address" value="{{ old('footer_contact_address', $settings['footer_contact_address'] ?? '') }}">
                    </div>
                    @error('footer_contact_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_contact_phone" class="form-label">Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control @error('footer_contact_phone') is-invalid @enderror" id="footer_contact_phone" name="footer_contact_phone" value="{{ old('footer_contact_phone', $settings['footer_contact_phone'] ?? '') }}">
                    </div>
                    @error('footer_contact_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="footer_contact_email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control @error('footer_contact_email') is-invalid @enderror" id="footer_contact_email" name="footer_contact_email" value="{{ old('footer_contact_email', $settings['footer_contact_email'] ?? '') }}">
                    </div>
                    @error('footer_contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="border-bottom pb-2">Copyright</h5>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="footer_copyright" class="form-label">Teks Copyright</label>
                    <input type="text" class="form-control @error('footer_copyright') is-invalid @enderror" id="footer_copyright" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright'] ?? '') }}">
                    <div class="form-text">Tahun akan ditambahkan secara otomatis.</div>
                    @error('footer_copyright')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
@endsection
