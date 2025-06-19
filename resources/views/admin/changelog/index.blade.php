@extends('admin.layouts.app')

@section('title', 'Changelog')

@section('styles')
<style>
    .changelog-header {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        color: white;
    }

    .changelog-card {
        transition: all 0.3s;
        margin-bottom: 1rem;
    }

    .changelog-item {
        padding: 1.5rem;
        border-left: 4px solid;
        background-color: #fff;
        margin-bottom: 1rem;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .changelog-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .changelog-item h5 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .feature {
        border-left-color: #4CAF50;
    }

    .feature-badge {
        background-color: #4CAF50;
        color: white;
    }

    .improvement {
        border-left-color: #2196F3;
    }

    .improvement-badge {
        background-color: #2196F3;
        color: white;
    }

    .bugfix {
        border-left-color: #FF9800;
    }

    .bugfix-badge {
        background-color: #FF9800;
        color: white;
    }

    .security {
        border-left-color: #F44336;
    }

    .security-badge {
        background-color: #F44336;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="changelog-header text-center">
            <h3 class="mb-2">Changelog - Riwayat Perubahan</h3>
            <p class="mb-0">Lacak semua pembaruan, perbaikan, dan fitur baru pada sistem</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @foreach($groupedChangelogs as $month => $logs)
            <div class="card mb-4 changelog-card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</h5>
                </div>
                <div class="card-body">
                    @foreach($logs as $changelog)
                        <div class="changelog-item {{ $changelog['type'] }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>
                                    {{ $changelog['title'] }}
                                    @if($changelog['is_major'])
                                        <span class="badge bg-danger ms-2">Major Update</span>
                                    @endif
                                </h5>
                                <div>
                                    <span class="badge {{ $changelog['type'] }}-badge">
                                        @if($changelog['type'] == 'feature')
                                            <i class="fas fa-star me-1"></i> Fitur Baru
                                        @elseif($changelog['type'] == 'improvement')
                                            <i class="fas fa-arrow-up me-1"></i> Peningkatan
                                        @elseif($changelog['type'] == 'bugfix')
                                            <i class="fas fa-bug me-1"></i> Perbaikan Bug
                                        @elseif($changelog['type'] == 'security')
                                            <i class="fas fa-shield-alt me-1"></i> Keamanan
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="mb-2">
                                @if(isset($changelog['version']))
                                    <span class="badge bg-dark">v{{ $changelog['version'] }}</span>
                                @endif
                                <span class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($changelog['release_date'])->format('d F Y') }}
                                </span>
                            </div>

                            @if(isset($changelog['description']))
                                <div class="mb-0">
                                    {{ $changelog['description'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
