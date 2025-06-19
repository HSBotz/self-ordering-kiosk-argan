<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $storeSettings['store_name'] ?? config('app.name', 'Kedai Coffee') }} - {{ $storeSettings['store_tagline'] ?? 'Self-Ordering Kiosk' }}</title>
    <!-- PWA Support -->
    <meta name="theme-color" content="#6b4226">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animations -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6a3412;
            --secondary-color: #a87d56;
            --accent-color: #ffb74d;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --text-color: #333333;
            --light-text-color: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: var(--dark-color);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(106, 52, 18, 0.2);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .card-body {
            padding: 1.5rem;
        }

        .price-tag {
            background-color: var(--accent-color);
            color: var(--primary-color);
            font-weight: 700;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .category-pill {
            cursor: pointer;
            border-radius: 25px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 8px;
            margin-bottom: 10px;
            border: 2px solid transparent;
            background-color: #f0f0f0;
            color: var(--text-color);
        }

        .category-pill:hover, .category-pill.active {
            background-color: var(--primary-color) !important;
            color: white !important;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(106, 52, 18, 0.2);
        }

        .cart-icon {
            position: relative;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -12px;
            background-color: var(--accent-color);
            color: var(--primary-color);
            font-weight: 700;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .page-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-weight: 300;
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }

        footer {
            background: linear-gradient(to right, var(--dark-color), #444);
            color: var(--light-text-color);
            padding: 40px 0 20px;
            margin-top: 60px;
            border-radius: 30px 30px 0 0;
        }

        footer h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--accent-color);
        }

        footer p {
            opacity: 0.8;
        }

        .social-icons a {
            color: var(--light-text-color);
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--accent-color);
            transform: translateY(-3px);
        }

        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Cart badge pulse animation */
        .pulse {
            animation: pulse 0.5s ease-out;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        /* Notification style */
        .notification {
            position: fixed;
            bottom: 20px; /* Pindah ke bagian bawah */
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 6px 10px;
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px); /* Animasi dari bawah ke atas */
            transition: all 0.3s ease;
            max-width: 200px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
        }

        .notification.show {
            opacity: 0.95;
            transform: translateY(0);
        }

        .notification-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .notification-icon {
            color: var(--accent-color);
            margin-right: 8px;
            font-size: 1.2rem;
        }

        .notification-text {
            flex-grow: 1;
        }

        .notification-close {
            color: white;
            background: none;
            border: none;
            font-size: 1rem;
            padding: 0;
            margin-left: 8px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .notification-close:hover {
            opacity: 1;
        }

        @media (max-width: 576px) {
            .notification {
                max-width: 90%;
                left: 50%;
                transform: translateX(-50%) translateY(20px);
                right: auto;
            }

            .notification.show {
                transform: translateX(-50%) translateY(0);
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('kiosk.index') }}">
                @if(isset($storeSettings['store_logo']) && !empty($storeSettings['store_logo']))
                    @if(in_array($storeSettings['store_logo'], ['icon_coffee', 'icon_mug', 'icon_store', 'icon_utensils']))
                        @if($storeSettings['store_logo'] === 'icon_coffee')
                            <i class="fas fa-coffee me-2"></i>
                        @elseif($storeSettings['store_logo'] === 'icon_mug')
                            <i class="fas fa-mug-hot me-2"></i>
                        @elseif($storeSettings['store_logo'] === 'icon_store')
                            <i class="fas fa-store me-2"></i>
                        @elseif($storeSettings['store_logo'] === 'icon_utensils')
                            <i class="fas fa-utensils me-2"></i>
                        @endif
                    @else
                        <img src="{{ asset($storeSettings['store_logo']) }}" alt="{{ $storeSettings['store_name'] ?? 'Kedai Coffee' }}" height="40" class="me-2">
                    @endif
                @else
                    <i class="fas fa-coffee me-2"></i>
                @endif
                {{ $storeSettings['store_name'] ?? 'Kedai Coffee' }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kiosk.index') ? 'active' : '' }}" href="{{ route('kiosk.index') }}">
                            <i class="fas fa-list-alt me-1"></i> Menu
                        </a>
                    </li>

                    <!-- Tombol Order Type -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="orderTypeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-concierge-bell me-1"></i>
                            <span id="current-order-type">
                                @if(session('order_type') == 'dine-in')
                                    Dine In
                                @elseif(session('order_type') == 'take-away')
                                    Take Away
                                @else
                                    Tipe Pesanan
                                @endif
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="orderTypeDropdown">
                            <li>
                                <form action="{{ route('kiosk.process-order-type') }}" method="POST" id="dine-in-form">
                                    @csrf
                                    <input type="hidden" name="order_type" value="dine-in">
                                    <button type="submit" class="dropdown-item {{ session('order_type') == 'dine-in' ? 'active' : '' }}">
                                        <i class="fas fa-utensils me-2"></i> Dine In
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('kiosk.process-order-type') }}" method="POST" id="take-away-form">
                                    @csrf
                                    <input type="hidden" name="order_type" value="take-away">
                                    <button type="submit" class="dropdown-item {{ session('order_type') == 'take-away' ? 'active' : '' }}">
                                        <i class="fas fa-shopping-bag me-2"></i> Take Away
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link cart-icon {{ request()->routeIs('kiosk.cart') ? 'active' : '' }}" href="{{ route('kiosk.cart') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(request()->routeIs('kiosk.index'))
    <header class="page-header">
        <div class="container">
            <h1 data-aos="fade-up">{{ $storeSettings['store_header_name'] ?? 'Kedai Coffee Kiosk' }}</h1>
            <p data-aos="fade-up" data-aos-delay="200">{{ $storeSettings['store_description'] ?? 'Nikmati berbagai pilihan kopi premium dan makanan lezat. Pesan dengan mudah melalui kiosk kami.' }}</p>
        </div>
    </header>
    @endif

    <main>
        <div class="container py-4">
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>{{ $footerSettings['footer_about_title'] ?? 'Kedai Coffee Kiosk' }}</h5>
                    <p>{{ $footerSettings['footer_about_text'] ?? 'Nikmati kopi premium kami dengan layanan self-ordering yang mudah dan cepat.' }}</p>
                    <!-- Debug info - akan dihapus nanti -->
                    <!-- Debug info has been removed -->

                    @php
                    $showSocialMedia = false;
                    if (!isset($footerSettings['footer_social_media_visible'])) {
                        $showSocialMedia = true; // default show if setting not found
                    } else {
                        $showSocialMedia = ($footerSettings['footer_social_media_visible'] === '1');
                    }
                    @endphp

                    @if($showSocialMedia)
                    <div class="social-icons mt-3">
                        <a href="{{ $footerSettings['footer_social_facebook'] ?? '#' }}"><i class="fab fa-facebook"></i></a>
                        <a href="{{ $footerSettings['footer_social_instagram'] ?? '#' }}"><i class="fab fa-instagram"></i></a>
                        <a href="{{ $footerSettings['footer_social_twitter'] ?? '#' }}"><i class="fab fa-twitter"></i></a>
                    </div>
                    @endif
                </div>
                <div class="col-md-4 mb-4">
                    <h5>{{ $footerSettings['footer_hours_title'] ?? 'Jam Buka' }}</h5>
                    <p>{{ $footerSettings['footer_hours_weekday'] ?? 'Senin - Jumat: 08:00 - 22:00' }}</p>
                    <p>{{ $footerSettings['footer_hours_weekend'] ?? 'Sabtu - Minggu: 09:00 - 23:00' }}</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>{{ $footerSettings['footer_contact_title'] ?? 'Kontak' }}</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> {{ $footerSettings['footer_contact_address'] ?? 'Jl. Kopi No. 123, Kota' }}</p>
                    <p><i class="fas fa-phone me-2"></i> {{ $footerSettings['footer_contact_phone'] ?? '+62 123 4567 890' }}</p>
                    <p><i class="fas fa-envelope me-2"></i> {{ $footerSettings['footer_contact_email'] ?? 'info@kedaicoffee.com' }}</p>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="opacity: 0.1;">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $footerSettings['footer_copyright'] ?? 'Kedai Coffee Kiosk. Semua hak dilindungi.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 800,
            once: true
        });

        // Fungsi untuk mengelola keranjang belanja
        function updateCartCount() {
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                let cart = [];
                if (sessionStorage.getItem('cart')) {
                    try {
                        cart = JSON.parse(sessionStorage.getItem('cart'));
                    } catch (e) {
                        console.error('Error parsing cart data from sessionStorage', e);
                        sessionStorage.removeItem('cart');
                    }
                }

                const count = cart.reduce((total, item) => total + item.quantity, 0);
                cartBadge.textContent = count;

                if (count > 0) {
                    cartBadge.style.display = 'inline-block';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        }

        // Fungsi untuk menangani pengiriman form tipe pesanan dengan AJAX
        function setupOrderTypeForms() {
            const dineInForm = document.getElementById('dine-in-form');
            const takeAwayForm = document.getElementById('take-away-form');
            const currentOrderTypeEl = document.getElementById('current-order-type');

            if (dineInForm && takeAwayForm) {
                // Handler untuk form Dine In
                dineInForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: new URLSearchParams(new FormData(this))
                    })
                    .then(response => {
                        if (response.ok) {
                            // Update UI
                            currentOrderTypeEl.textContent = 'Dine In';

                            // Tambahkan kelas active ke item dropdown yang dipilih
                            document.querySelector('#dine-in-form .dropdown-item').classList.add('active');
                            document.querySelector('#take-away-form .dropdown-item').classList.remove('active');

                            // Tampilkan notifikasi
                            showNotification('Tipe pesanan diubah menjadi Dine In');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });

                // Handler untuk form Take Away
                takeAwayForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: new URLSearchParams(new FormData(this))
                    })
                    .then(response => {
                        if (response.ok) {
                            // Update UI
                            currentOrderTypeEl.textContent = 'Take Away';

                            // Tambahkan kelas active ke item dropdown yang dipilih
                            document.querySelector('#take-away-form .dropdown-item').classList.add('active');
                            document.querySelector('#dine-in-form .dropdown-item').classList.remove('active');

                            // Tampilkan notifikasi
                            showNotification('Tipe pesanan diubah menjadi Take Away');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            }
        }

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message) {
            // Hapus notifikasi yang sudah ada jika masih ada
            const existingNotif = document.querySelector('.notification');
            if (existingNotif) {
                document.body.removeChild(existingNotif);
            }

            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-check-circle notification-icon"></i>
                    <span>${message}</span>
                    <i class="fas fa-times notification-close" style="margin-left: 5px; cursor: pointer; font-size: 0.8rem;"></i>
                </div>
            `;

            document.body.appendChild(notification);

            // Tambahkan event listener untuk tombol tutup
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            });

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 1500); // Mengurangi waktu tampil menjadi 1.5 detik
        }

        // Inisialisasi hitungan keranjang
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            setupOrderTypeForms();

            // Tambahkan event listener untuk perubahan di sessionStorage
            window.addEventListener('storage', function(e) {
                if (e.key === 'cart') {
                    updateCartCount();
                }
            });
        });
    </script>

    @yield('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed: ', error);
                    });
            });
        }

        // Add to Home Screen prompt
        let deferredPrompt;
        const addToHomeBtn = document.createElement('button');
        addToHomeBtn.style.display = 'none';

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            // Show the "Add to Home Screen" button
            showInstallPromotion();
        });

        function showInstallPromotion() {
            // Buat notifikasi untuk menginstal aplikasi
            const notification = document.createElement('div');
            notification.className = 'pwa-install-prompt';
            notification.innerHTML = `
                <div class="pwa-install-content">
                    <p>Instal aplikasi Kedai Coffee Kiosk di perangkat Anda</p>
                    <button id="pwa-install-btn" class="btn btn-sm btn-primary">Instal Aplikasi</button>
                    <button id="pwa-dismiss-btn" class="btn btn-sm btn-outline-secondary ms-2">Nanti</button>
                </div>
            `;

            document.body.appendChild(notification);

            // Tambahkan event listener untuk tombol instal
            document.getElementById('pwa-install-btn').addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    deferredPrompt = null;
                }
                document.body.removeChild(notification);
            });

            // Tambahkan event listener untuk tombol dismiss
            document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                document.body.removeChild(notification);
            });
        }
    </script>
</body>
</html>

