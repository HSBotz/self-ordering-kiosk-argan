@extends('layouts.app')

@section('styles')
<style>
    .success-container {
        padding: 0.5rem 0;
        text-align: center;
    }

    .success-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        color: white;
        font-size: 1.6rem;
        box-shadow: 0 3px 6px rgba(106, 52, 18, 0.3);
    }

    .success-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        padding: 0.8rem;
        max-width: 500px;
        margin: 0 auto;
        font-size: 0.9rem;
    }

    .order-number {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.1rem;
    }

    .thank-you-message {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .order-info {
        margin: 0.5rem 0;
        padding: 0.6rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        font-size: 0.8rem;
    }

    .order-info p {
        margin-bottom: 0.2rem;
    }

    .order-info .row > div:first-child {
        border-right: 1px solid #eee;
    }

    .order-items-container {
        max-height: 110px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .order-items-container::-webkit-scrollbar {
        width: 3px;
    }

    .order-items-container::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 8px;
    }

    .order-item {
        padding: 0.4rem 0.6rem;
        border-bottom: 1px solid #eee;
        font-size: 0.75rem;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-item-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-item-name {
        font-weight: 600;
    }

    .order-item-quantity {
        color: #666;
        font-size: 0.7rem;
        margin-left: 0.2rem;
    }

    .order-item-price {
        font-weight: 600;
    }

    .confetti {
        position: absolute;
        width: 6px;
        height: 6px;
        background-color: #f2d74e;
        opacity: 0;
    }

    .redirect-info {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }

    .action-buttons {
        margin-top: 0.5rem;
    }

    .btn-sm-custom {
        padding: 0.25rem 0.6rem;
        font-size: 0.8rem;
    }

    .compact-layout .badge {
        font-size: 0.65rem;
        padding: 0.2em 0.4em;
    }

    .compact-layout .fs-5 {
        font-size: 1rem !important;
    }

    .compact-layout .lead {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .compact-layout .small {
        font-size: 0.75rem;
    }

    .compact-layout h5 {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
        margin-top: 0.3rem;
    }

    .order-item-variant {
        display: inline-block;
        font-size: 0.65rem;
        padding: 0.1rem 0.3rem;
        border-radius: 3px;
        margin-left: 0.3rem;
        vertical-align: middle;
        font-weight: 600;
    }

    .variant-hot {
        background-color: #ffcccb;
        color: #d63031;
        border: 1px solid #d63031;
    }

    .variant-ice {
        background-color: #c7ecee;
        color: #0984e3;
        border: 1px solid #0984e3;
    }

    .variant-header {
        font-size: 0.8rem;
        margin-top: 0.2rem;
        text-align: left;
        color: #666;
    }

    .variant-info {
        display: flex;
        align-items: center;
        margin-top: 0.2rem;
    }

    .variant-count {
        margin-left: auto;
        font-weight: bold;
        font-size: 0.75rem;
    }

    /* QR Code Styles */
    .qr-code-container {
        margin: 0.5rem 0;
    }

    #qrcode {
        display: flex;
        justify-content: center;
    }

    #qrcode img {
        max-width: 100%;
        height: auto;
        max-height: 120px;
    }

    /* Optimasi untuk tampilan tanpa scrolling */
    .card.p-3 {
        padding: 0.6rem !important;
        max-width: 200px !important;
    }

    .mb-2 {
        margin-bottom: 0.3rem !important;
    }

    .my-3 {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }

    .alert {
        padding: 0.3rem 0.5rem;
        margin-bottom: 0;
    }
</style>
@endsection

@section('content')
<div class="success-container">
    <div class="success-card compact-layout" data-aos="zoom-in">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="thank-you-message">Terima Kasih!</h1>
        <p class="lead">Pesanan Anda telah berhasil diterima</p>

        <div class="order-number">
            No. Pesanan: {{ $order->order_number }}
        </div>

        <div class="order-info">
            <div class="row g-1">
                <div class="col-6 text-start">
                    <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/y H:i') }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
                </div>
                <div class="col-6 text-end">
                    <p><strong>Pembayaran:</strong> {{ ucfirst($order->payment_method ?? 'Tunai') }}</p>
                    <p>
                        <strong>Tipe:</strong>
                        @if($order->order_type == 'dine-in')
                            <span class="badge bg-info"><i class="fas fa-utensils me-1"></i>Dine In</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-shopping-bag me-1"></i>Take Away</span>
                        @endif
                    </p>
                    <p><strong>Total:</strong> <span class="fs-5 fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                </div>
            </div>
        </div>

        <!-- QR Code untuk pelacakan pesanan -->
        @php
            use App\Helpers\SettingsHelper;

            // Pastikan pengaturan dibaca ulang dari database, bukan dari cache
            $showQrCode = \App\Models\SiteSetting::where('key', 'show_qr_code')->first();
            $qrCodeVisible = $showQrCode ? ($showQrCode->value == '1') : true;

            // Ambil ukuran QR dari database
            $qrCodeSizeSetting = \App\Models\SiteSetting::where('key', 'qr_code_size')->first();
            $qrCodeSize = $qrCodeSizeSetting ? $qrCodeSizeSetting->value : 'medium';

            $qrImageSize = 5; // Default size for medium

            if ($qrCodeSize == 'small') {
                $qrImageSize = 3;
            } elseif ($qrCodeSize == 'large') {
                $qrImageSize = 7;
            }

            // Debug
            \Illuminate\Support\Facades\Log::info('QR Code Settings:', [
                'show_qr_code_raw' => $showQrCode ? $showQrCode->value : 'not set',
                'qr_code_visible' => $qrCodeVisible ? 'true' : 'false',
                'qr_code_size' => $qrCodeSize,
                'qr_image_size' => $qrImageSize
            ]);
        @endphp

        @if($qrCodeVisible)
        <div class="qr-code-container text-center my-2">
            <div class="card p-3 mb-2 mx-auto">
                <div class="qr-code-image mb-1">
                    <div id="qrcode"></div>
                </div>
                <div class="qr-code-info small">
                    <p class="mb-0 fw-bold">Scan untuk melacak pesanan</p>
                    <p class="text-muted mb-0 small">Pantau status pesanan Anda</p>
                    <a href="{{ $trackingUrl }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div>
            <h5 class="text-start">Detail Pesanan</h5>

            <div class="variant-header mb-1">
                @php
                    $hotCount = $order->orderItems->where('variant_type', 'hot')->count();
                    $iceCount = $order->orderItems->where('variant_type', 'ice')->count();
                @endphp

                @if($hotCount > 0 || $iceCount > 0)
                    <div class="small text-muted mb-1">Ringkasan Varian:</div>

                    @if($hotCount > 0)
                    <div class="variant-info">
                        <span class="order-item-variant variant-hot me-1">
                            <i class="fas fa-mug-hot"></i> Panas
                        </span>
                        <span class="variant-count">{{ $hotCount }} item</span>
                    </div>
                    @endif

                    @if($iceCount > 0)
                    <div class="variant-info">
                        <span class="order-item-variant variant-ice me-1">
                            <i class="fas fa-cube"></i> Dingin
                        </span>
                        <span class="variant-count">{{ $iceCount }} item</span>
                    </div>
                    @endif
                @endif
            </div>

            <div class="order-items-container">
                @foreach($order->orderItems as $item)
                <div class="order-item">
                    <div class="order-item-details">
                        <div>
                            <span class="order-item-name">{{ $item->product->name }}</span>
                            @if($item->variant_type == 'hot')
                                <span class="order-item-variant variant-hot">
                                    <i class="fas fa-mug-hot"></i> Panas
                                </span>
                            @elseif($item->variant_type == 'ice')
                                <span class="order-item-variant variant-ice">
                                    <i class="fas fa-cube"></i> Dingin
                                </span>
                            @endif
                            <span class="order-item-quantity">x{{ $item->quantity }}</span>
                        </div>
                        <div class="order-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-muted mb-1 small">Pesanan Anda sedang diproses dan akan segera disajikan.</p>
        <div class="action-buttons">
            <a href="{{ route('kiosk.index') }}" class="btn btn-primary btn-sm-custom">
                <i class="fas fa-home me-1"></i> Kembali ke Menu
            </a>
            <div class="alert alert-info redirect-info">
                <i class="fas fa-clock me-1"></i> Halaman akan dialihkan dalam <span id="countdown">15</span> detik
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- QR Code Library -->
@if($qrCodeVisible)
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR Code
        @if($qrCodeVisible)
        try {
            console.log('Generating QR code for URL: {{ $trackingUrl }}');
            var typeNumber = 0;
            var errorCorrectionLevel = 'L';
            var qr = qrcode(typeNumber, errorCorrectionLevel);
            qr.addData('{{ $trackingUrl }}');
            qr.make();
            document.getElementById('qrcode').innerHTML = qr.createImgTag({{ $qrImageSize }});
            console.log('QR code generated successfully');

            // Tambahkan hidden link yang bisa diklik untuk testing
            var testLink = document.createElement('a');
            testLink.href = '{{ $trackingUrl }}';
            testLink.textContent = 'Test tracking link';
            testLink.style.fontSize = '10px';
            testLink.style.display = 'none';
            document.getElementById('qrcode').appendChild(testLink);
        } catch (error) {
            console.error('Error generating QR code:', error);
        }
        @endif

        // Bersihkan keranjang belanja setelah checkout berhasil
        sessionStorage.removeItem('cart');
        // Bersihkan data checkout
        sessionStorage.removeItem('checkout_data');
        // Bersihkan tipe pesanan
        sessionStorage.removeItem('orderType');

        // Update cart count in the header
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            cartBadge.textContent = '0';
            cartBadge.style.display = 'none';
        }

        // Trigger storage event untuk halaman lain
        if (typeof window.dispatchEvent === 'function') {
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'cart'
            }));
        }

        // Jalankan animasi confetti
        createConfetti();

        // Fungsi untuk membuat confetti
        function createConfetti() {
            const colors = ['#f2d74e', '#95c3de', '#ff9a91', '#f2a0ac', '#9fe89f'];
            const container = document.querySelector('.success-container');

            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = -10 + 'px';
                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';

                container.appendChild(confetti);

                animateConfetti(confetti);
            }
        }

        function animateConfetti(confetti) {
            const duration = Math.random() * 2 + 1;
            const delay = Math.random() * 1;

            confetti.animate([
                {
                    top: '-10px',
                    opacity: 1,
                    transform: 'rotate(' + (Math.random() * 360) + 'deg)'
                },
                {
                    top: '100vh',
                    opacity: 0,
                    transform: 'rotate(' + (Math.random() * 360 + 720) + 'deg)'
                }
            ], {
                duration: duration * 1000,
                delay: delay * 1000,
                fill: 'forwards',
                easing: 'cubic-bezier(0.25, 1, 0.5, 1)'
            });

            setTimeout(() => {
                confetti.remove();
            }, (duration + delay) * 1000);
        }

        // Redirect otomatis ke halaman order-type setelah 15 detik
        let secondsLeft = 15;
        const countdownElement = document.getElementById('countdown');

        const countdownInterval = setInterval(function() {
            secondsLeft--;
            countdownElement.textContent = secondsLeft;

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "{{ route('kiosk.order-type') }}";
            }
        }, 1000);
    });
</script>
@endsection
