@extends('layouts.app')

@section('styles')
<style>
    .tracking-container {
        padding: 1rem 0;
    }

    .tracking-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        max-width: 550px;
        margin: 0 auto;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .order-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.2rem;
    }

    .order-info {
        margin: 1rem 0;
        padding: 0.8rem;
        background-color: #f8f9fa;
        border-radius: 10px;
        font-size: 0.85rem;
    }

    .order-info p {
        margin-bottom: 0.3rem;
    }

    .order-items-container {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .order-item {
        padding: 0.5rem 0.8rem;
        border-bottom: 1px solid #eee;
        font-size: 0.8rem;
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
        font-size: 0.75rem;
        margin-left: 0.3rem;
    }

    .order-item-price {
        font-weight: 600;
    }

    .order-item-variant {
        display: inline-block;
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        margin-left: 0.4rem;
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

    .tracking-steps {
        margin: 2rem 0;
        position: relative;
    }

    .tracking-line {
        position: absolute;
        top: 30px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .tracking-progress {
        position: absolute;
        top: 30px;
        left: 0;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        z-index: 2;
        transition: width 0.5s ease;
    }

    .tracking-steps-container {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 3;
    }

    .tracking-step {
        text-align: center;
        width: 25%;
    }

    .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        color: #6c757d;
        font-size: 1.5rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .step-active .step-icon {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: 0 5px 10px rgba(106, 52, 18, 0.3);
    }

    .step-completed .step-icon {
        background-color: #28a745;
        color: white;
    }

    .step-cancelled .step-icon {
        background-color: #dc3545;
        color: white;
    }

    .step-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
    }

    .step-active .step-label {
        color: var(--primary-color);
    }

    .step-completed .step-label {
        color: #28a745;
    }

    .step-cancelled .step-label {
        color: #dc3545;
    }

    .refresh-button {
        display: block;
        margin: 1rem auto;
    }

    .last-updated {
        text-align: center;
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    /* Notifikasi */
    .notification-banner {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background-color: #28a745;
        color: white;
        padding: 15px;
        text-align: center;
        z-index: 9999;
        font-size: 18px;
        animation: slideDown 0.5s ease-out;
        display: none;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
        }
        to {
            transform: translateY(0);
        }
    }
    
    /* Tombol putar notifikasi */
    #play-notification-btn {
        display: none;
        margin-left: 10px;
        vertical-align: middle;
    }
    
    /* Indikator suara */
    .sound-indicator {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
        font-size: 14px;
        background-color: rgba(255,255,255,0.3);
        padding: 2px 8px;
        border-radius: 12px;
    }
    
    .sound-indicator i {
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(0.95); opacity: 0.7; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.7; }
    }

    @media (max-width: 576px) {
        .step-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .step-label {
            font-size: 0.7rem;
        }

        .tracking-line, .tracking-progress {
            top: 20px;
        }
    }
</style>
@endsection

@section('content')
<!-- Audio untuk notifikasi -->
<audio id="notification-sound" preload="auto" loop="loop" playsinline>
    <source src="{{ asset('audio/order-completed.mp3') }}" type="audio/mpeg">
    <source src="{{ asset('audio/order-completed.wav') }}" type="audio/wav">
    Maaf, browser Anda tidak mendukung elemen audio.
</audio>

<!-- Banner notifikasi -->
<div id="notification-banner" class="notification-banner">
    <i class="fas fa-bell me-2"></i> 
    Pesanan Anda telah selesai dan siap diambil!
    <span id="sound-indicator" class="sound-indicator">
        <i class="fas fa-volume-up me-1"></i> Suara aktif
    </span>
    <button id="play-notification-btn" class="btn btn-sm btn-light">
        <i class="fas fa-volume-up me-1"></i> Putar Notifikasi
    </button>
</div>

<div class="tracking-container">
    <div class="tracking-card" data-aos="fade-up">
        <div class="order-header">
            <div>
                <h1 class="order-number">Pesanan #{{ $order->order_number }}</h1>
                <p class="text-muted small mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                @if($order->status == 'pending')
                    <span class="badge bg-warning">Pending</span>
                @elseif($order->status == 'processing')
                    <span class="badge bg-info">Sedang Diproses</span>
                @elseif($order->status == 'completed')
                    <span class="badge bg-success">Selesai</span>
                @elseif($order->status == 'cancelled')
                    <span class="badge bg-danger">Dibatalkan</span>
                @endif
            </div>
        </div>

        <div class="order-info">
            <div class="row g-2">
                <div class="col-6 text-start">
                    <p><strong>Nama:</strong> {{ $order->customer_name ?? 'Anonim' }}</p>
                    <p>
                        <strong>Tipe:</strong>
                        @if($order->order_type == 'dine-in')
                            <span class="badge bg-info"><i class="fas fa-utensils me-1"></i>Dine In</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-shopping-bag me-1"></i>Take Away</span>
                        @endif
                    </p>
                </div>
                <div class="col-6 text-end">
                    <p><strong>Pembayaran:</strong> {{ ucfirst($order->payment_method ?? 'Tunai') }}</p>
                    <p><strong>Total:</strong> <span class="fs-5 fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></p>
                </div>
            </div>
        </div>

        <div class="tracking-steps">
            <div class="tracking-line"></div>
            @php
                $progressWidth = 0;
                if ($order->status == 'pending') {
                    $progressWidth = 25;
                } elseif ($order->status == 'processing') {
                    $progressWidth = 50;
                } elseif ($order->status == 'completed') {
                    $progressWidth = 100;
                } elseif ($order->status == 'cancelled') {
                    $progressWidth = 100;
                }
            @endphp
            <div class="tracking-progress" style="width: {{ $progressWidth }}%;"></div>
            <div class="tracking-steps-container">
                <div class="tracking-step {{ $order->status != 'cancelled' ? 'step-completed' : '' }}">
                    <div class="step-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="step-label">Diterima</div>
                </div>
                <div class="tracking-step {{ $order->status == 'processing' || $order->status == 'completed' ? 'step-completed' : ($order->status == 'pending' ? 'step-active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div class="step-label">Diproses</div>
                </div>
                <div class="tracking-step {{ $order->status == 'completed' ? 'step-completed' : ($order->status == 'processing' ? 'step-active' : '') }}">
                    <div class="step-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Selesai</div>
                </div>
                <div class="tracking-step {{ $order->status == 'cancelled' ? 'step-cancelled' : '' }}">
                    <div class="step-icon">
                        <i class="fas {{ $order->status == 'cancelled' ? 'fa-times' : 'fa-times' }}"></i>
                    </div>
                    <div class="step-label">Dibatalkan</div>
                </div>
            </div>
        </div>

        <div>
            <h5 class="text-start">Detail Pesanan</h5>
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

        <button id="refresh-btn" class="btn btn-sm btn-outline-primary refresh-button">
            <i class="fas fa-sync-alt me-1"></i> Refresh Status
        </button>
        <div class="last-updated">
            Terakhir diperbarui: <span id="last-updated-time">{{ now()->format('H:i:s') }}</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tambahkan script notification helper -->
<script src="{{ asset('js/notification-helper.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi variabel
        const orderNumber = '{{ $order->order_number }}';
        const currentStatus = '{{ $order->status }}';
        const notificationSound = document.getElementById('notification-sound');
        const notificationBanner = document.getElementById('notification-banner');
        const playNotificationBtn = document.getElementById('play-notification-btn');
        const soundIndicator = document.getElementById('sound-indicator');
        let lastStatus = currentStatus;
        let isPesananSelesai = currentStatus === 'completed';
        let apiCallAttempts = 0;
        let apiFailures = 0;
        let useAlternativeApi = false;

        // Inisialisasi audio notification manager
        const audioManager = NotificationHelper.createAudioManager(
            notificationSound,
            [
                '{{ asset("audio/order-completed.mp3") }}', 
                '{{ asset("audio/order-completed.wav") }}'
            ]
        );
        
        // Register service worker untuk notifikasi background
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw-notification.js')
                .then(registration => {
                    console.log('Service Worker terdaftar dengan scope:', registration.scope);
                })
                .catch(error => {
                    console.error('Registrasi Service Worker gagal:', error);
                });
        }
        
        // Event listener untuk tombol putar notifikasi
        playNotificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            playNotificationSound(true); // Force play
            this.style.display = 'none';
            soundIndicator.style.display = 'inline-flex';
        });
        
        // Fungsi untuk meminta izin notifikasi
        function requestNotificationPermission() {
            NotificationHelper.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Izin notifikasi diberikan');
                }
            });
        }

        // Fungsi untuk menampilkan notifikasi
        function showNotification() {
            NotificationHelper.showNotification('Pesanan Anda Siap!', {
                body: 'Pesanan #' + orderNumber + ' telah selesai dan siap diambil!',
                icon: '/images/logo.png',
                tag: 'order-completed-' + orderNumber,
                renotify: true,
                requireInteraction: true,
                data: { orderNumber }
            });
        }

        // Fungsi untuk memainkan suara notifikasi
        function playNotificationSound(forcePlay = false) {
            // Gunakan audio manager untuk mengatasi masalah autoplay
            const success = audioManager.play(forcePlay, true);
            
            // Update UI berdasarkan hasil pemutaran
            if (success) {
                soundIndicator.style.display = 'inline-flex';
                playNotificationBtn.style.display = 'none';
            } else {
                soundIndicator.style.display = 'none';
                playNotificationBtn.style.display = 'inline-block';
            }
            
            return success;
        }

        // Fungsi untuk menghentikan pemutaran suara
        function stopNotificationSound() {
            audioManager.stop();
            soundIndicator.style.display = 'none';
        }

        // Fungsi untuk menampilkan banner notifikasi
        function showNotificationBanner() {
            notificationBanner.style.display = 'block';
        }

        // Fungsi untuk memeriksa status pesanan
        function checkOrderStatus() {
            // Tentukan URL API berdasarkan kondisi
            let apiUrl = useAlternativeApi 
                ? '/track-api/' + orderNumber 
                : '/api/track/' + orderNumber;

            // Menyiapkan parameter timestamp untuk mencegah caching
            apiUrl += '?t=' + new Date().getTime();

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response error: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        apiCallAttempts++;
                        apiFailures = 0; // Reset failure counter on success
                        
                        const order = data.order;
                        const newStatus = order.status;
                        
                        // Update last updated time
                        document.getElementById('last-updated-time').textContent = new Date().toLocaleTimeString();
                        
                        // Jika status berubah, update UI
                        if (newStatus !== lastStatus) {
                            console.log(`Status berubah dari ${lastStatus} menjadi ${newStatus}`);
                            
                            // Perbarui UI
                            updateStatusUI(newStatus);
                            
                            // Periksa apakah pesanan selesai
                            if (newStatus === 'completed' && !isPesananSelesai) {
                                isPesananSelesai = true;
                                
                                // Simpan bahwa notifikasi sudah ditampilkan
                                localStorage.setItem('notification_shown_' + orderNumber, 'true');
                                
                                // Tampilkan notifikasi
                                showNotification();
                                showNotificationBanner();
                                
                                // Mainkan suara dengan interaksi pengguna
                                // Ini akan memicu browser untuk meminta izin jika diperlukan
                                playNotificationSound(true);
                                
                                // Jika halaman dalam mode latar belakang, kirim ke service worker
                                if (document.visibilityState === 'hidden' && 'serviceWorker' in navigator) {
                                    navigator.serviceWorker.ready.then(registration => {
                                        registration.active.postMessage({
                                            type: 'ORDER_COMPLETED',
                                            orderNumber: orderNumber
                                        });
                                    });
                                }
                            }
                            
                            // Perbarui status terakhir
                            lastStatus = newStatus;
                        }
                    }
                })
                .catch(error => {
                    console.error('Gagal memeriksa status pesanan:', error);
                    apiFailures++;
                    
                    // Debug info
                    console.log(`Debug: Error: ${error.message}, Attempts: ${apiCallAttempts}`);
                    
                    // Jika gagal beberapa kali, coba gunakan API alternatif
                    if (apiFailures >= 3 && !useAlternativeApi) {
                        console.log('Beralih ke API alternatif');
                        useAlternativeApi = true;
                    }
                });
        }

        // Fungsi untuk memperbarui UI status
        function updateStatusUI(status) {
            // Update badge status
            const statusBadgeContainer = document.querySelector('.order-header > div:last-child');
            let statusBadge;
            
            if (status === 'pending') {
                statusBadge = '<span class="badge bg-warning">Pending</span>';
            } else if (status === 'processing') {
                statusBadge = '<span class="badge bg-info">Sedang Diproses</span>';
            } else if (status === 'completed') {
                statusBadge = '<span class="badge bg-success">Selesai</span>';
            } else if (status === 'cancelled') {
                statusBadge = '<span class="badge bg-danger">Dibatalkan</span>';
            }
            
            statusBadgeContainer.innerHTML = statusBadge;

            // Hitung progress bar
            let progressWidth = 0;
            if (status === 'pending') {
                progressWidth = 25;
            } else if (status === 'processing') {
                progressWidth = 50;
            } else if (status === 'completed' || status === 'cancelled') {
                progressWidth = 100;
            }
            
            // Update progress bar
            document.querySelector('.tracking-progress').style.width = progressWidth + '%';

            // Update tracking steps
            const steps = document.querySelectorAll('.tracking-step');
            
            // Reset all steps
            steps.forEach(step => {
                step.classList.remove('step-active', 'step-completed', 'step-cancelled');
            });

            // Langkah 1: Diterima (selalu completed kecuali cancelled)
            if (status !== 'cancelled') {
                steps[0].classList.add('step-completed');
            }

            // Langkah 2: Diproses
            if (status === 'processing' || status === 'completed') {
                steps[1].classList.add('step-completed');
            } else if (status === 'pending') {
                steps[1].classList.add('step-active');
            }

            // Langkah 3: Selesai
            if (status === 'completed') {
                steps[2].classList.add('step-completed');
            } else if (status === 'processing') {
                steps[2].classList.add('step-active');
            }

            // Langkah 4: Dibatalkan
            if (status === 'cancelled') {
                steps[3].classList.add('step-cancelled');
            }
        }

        // Periksa apakah notifikasi sudah pernah ditampilkan
        function checkPreviousNotification() {
            const notificationShown = localStorage.getItem('notification_shown_' + orderNumber);
            
            // Jika pesanan sudah selesai
            if (currentStatus === 'completed') {
                // Tandai bahwa pesanan sudah selesai
                isPesananSelesai = true;
                
                // Tampilkan notifikasi jika belum ditampilkan
                if (!notificationShown) {
                    // Simpan bahwa notifikasi sudah ditampilkan
                    localStorage.setItem('notification_shown_' + orderNumber, 'true');
                }
                
                // Tampilkan banner dan putar suara
                showNotificationBanner();
                
                // Coba putar suara otomatis, jika gagal akan menampilkan tombol play
                playNotificationSound();
            }
        }

        // Cek notifikasi sebelumnya saat halaman dimuat
        checkPreviousNotification();
        
        // Minta izin notifikasi
        requestNotificationPermission();

        // Auto refresh setiap 5 detik
        const refreshInterval = setInterval(checkOrderStatus, 5000);

        // Manual refresh dengan tombol
        document.getElementById('refresh-btn').addEventListener('click', function() {
            checkOrderStatus();
            this.disabled = true;
            setTimeout(() => {
                this.disabled = false;
            }, 2000); // Prevent spam clicking
        });

        // Fungsi handler saat halaman dibuka dari notifikasi
        function handleNotificationAction() {
            if (window.location.hash === '#notification') {
                // Jika halaman dibuka dari notifikasi, langsung mainkan suara
                if (isPesananSelesai) {
                    showNotificationBanner();
                    playNotificationSound(true); // Force play
                }
                // Hapus hash dari URL
                history.replaceState(null, null, ' ');
            }
        }

        // Check hash when page loads
        handleNotificationAction();

        // Buat Service Worker untuk notifikasi background berhasil
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'NOTIFICATION_CLICKED') {
                    // Fokus ke halaman jika notif diklik
                    window.focus();
                    // Mainkan suara lagi jika halaman difokuskan
                    if (isPesananSelesai) {
                        playNotificationSound(true); // Force play
                        showNotificationBanner();
                    }
                }
            });
        }

        // Event listener ketika dokumen menjadi visible lagi
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Segera periksa status saat tab difokuskan
                checkOrderStatus();
                
                // Jika pesanan sudah selesai, pastikan audio berjalan
                if (isPesananSelesai) {
                    showNotificationBanner();
                    if (!audioManager.isPlaying) {
                        playNotificationSound(true);
                    }
                }
            }
        });

        // Ketika halaman akan ditutup (unload), kirim sinyal ke service worker untuk melanjutkan pemutaran suara
        window.addEventListener('beforeunload', function() {
            if (isPesananSelesai && 'serviceWorker' in navigator) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.active.postMessage({
                        type: 'PAGE_UNLOADING',
                        orderNumber: orderNumber,
                        completed: isPesananSelesai
                    });
                });
            }
        });

        // Special fix untuk iOS: tambahkan event listener untuk unlock audio setelah interaksi pengguna pertama
        function unlockAudio() {
            if (isPesananSelesai) {
                playNotificationSound(true);
            }
            document.removeEventListener('touchstart', unlockAudio);
            document.removeEventListener('touchend', unlockAudio);
            document.removeEventListener('click', unlockAudio);
        }

        document.addEventListener('touchstart', unlockAudio);
        document.addEventListener('touchend', unlockAudio);
        document.addEventListener('click', unlockAudio);
    });
</script>
@endsection 