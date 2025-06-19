@extends('admin.layouts.app')

@section('title', 'Tentang Aplikasi')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Tentang Aplikasi</span>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-4">
                        <i class="fas fa-mug-hot fa-4x text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $appInfo['name'] }}</h2>
                        <p class="text-muted mb-0">Versi {{ $appInfo['version'] }}</p>
                    </div>
                </div>

                <p class="lead">{{ $appInfo['description'] }}</p>

                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal Rilis:</strong> {{ \Carbon\Carbon::parse($appInfo['release_date'])->format('d M Y') }}</p>
                            <p><strong>Update Terakhir:</strong> {{ \Carbon\Carbon::parse($appInfo['last_update'])->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Framework:</strong> {{ $appInfo['framework'] }}</p>
                            <p><strong>PHP Version:</strong> {{ $appInfo['php_version'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Hak Cipta</h5>
                        <hr>
                        <p><strong>Pengembang:</strong> {{ $appInfo['developer'] }}</p>
                        <p><strong>Publisher:</strong> {{ $appInfo['publisher'] }}</p>
                        <p class="mb-0"><strong>Hak Cipta:</strong> {{ $appInfo['copyright'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h3 class="mb-4">Panduan Penggunaan Aplikasi</h3>

            <div class="accordion" id="userGuideAccordion">
                @foreach($userGuides as $index => $guide)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                {{ $guide['title'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#userGuideAccordion">
                            <div class="accordion-body">
                                @foreach($guide['sections'] as $section)
                                    <div class="mb-4">
                                        <h5>{{ $section['title'] }}</h5>
                                        <p>{{ $section['content'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Dukungan Teknis</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Butuh bantuan?</h5>
                <p>Jika Anda memiliki pertanyaan atau mengalami masalah dengan aplikasi, silakan hubungi tim dukungan kami:</p>
                <ul>
                    <li>Email: <a href="mailto:alcateambot@gmail.com">alcateambot@gmail.com</a></li>
                    <li>Telepon: <a href="https://wa.me/6281340078956">+6281340078956</a></li>
                    <li>Jam Kerja: Senin - Jumat, 09:00 - 17:00 WIB</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Sumber Daya</h5>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-file-pdf me-3 text-danger"></i>
                        <span>Panduan Pengguna Lengkap (PDF)</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-video me-3 text-primary"></i>
                        <span>Video Tutorial</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-question-circle me-3 text-success"></i>
                        <span>FAQ</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        <p class="mb-0">{{ $appInfo['copyright'] }}</p>
    </div>
</div>
@endsection
