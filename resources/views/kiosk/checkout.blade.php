@extends('layouts.app')

@section('styles')
<style>
    .order-summary-card {
        border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 60px;
    }

    .checkout-form-card {
        border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .form-control, .form-select {
        padding: 0.4rem 0.6rem;
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(106, 52, 18, 0.2);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.2rem;
        color: #555;
        font-size: 0.85rem;
    }

    .payment-method-item {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .payment-method-item.active {
        border-color: var(--primary-color);
        background-color: rgba(106, 52, 18, 0.05);
    }

    .payment-method-item:hover {
        border-color: var(--primary-color);
    }

    .payment-icon {
        font-size: 1.2rem;
        color: var(--secondary-color);
        margin-right: 0.5rem;
    }

    .order-btn {
        padding: 6px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .order-summary-item {
        margin-bottom: 0.4rem;
        padding-bottom: 0.4rem;
        border-bottom: 1px solid #eee;
    }

    .back-to-cart {
        color: var(--secondary-color);
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.85rem;
    }

    .back-to-cart:hover {
        color: var(--primary-color);
        text-decoration: underline;
    }

    /* Style untuk QRIS */
    #qris-container {
        display: none;
        text-align: center;
        margin-top: 0.4rem;
        padding: 0.4rem;
        border: 1px dashed #ccc;
        border-radius: 6px;
        background-color: #f8f9fa;
    }

    #qris-container.show {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    #qris-container img {
        max-width: 100%;
        max-height: 150px;
        margin-bottom: 0.4rem;
        cursor: zoom-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Style untuk modal QRIS */
    .qris-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        animation: fadeIn 0.3s ease;
    }

    .qris-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
        text-align: center;
        background-color: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .qris-modal-content img {
        max-width: 100%;
        max-height: 70vh;
        display: block;
        margin: 0 auto 0.5rem;
    }

    .qris-modal-close {
        position: absolute;
        top: 5px;
        right: 8px;
        font-size: 1.5rem;
        color: #555;
        cursor: pointer;
        transition: all 0.2s;
    }

    .qris-modal-close:hover {
        color: var(--primary-color);
    }

    .btn-zoom {
        background-color: var(--secondary-color);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 0.3rem 0.6rem;
        margin-top: 0.3rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-zoom:hover {
        background-color: var(--primary-color);
    }

    .variant-badge {
        font-size: 0.65rem;
        padding: 0.1rem 0.25rem;
        border-radius: 3px;
        margin-left: 0.2rem;
        display: inline-block;
    }

    .variant-hot {
        background-color: #ffcccb;
        color: #d63031;
    }

    .variant-ice {
        background-color: #c7ecee;
        color: #0984e3;
    }

    /* Optimasi tambahan */
    .card-header {
        padding: 0.4rem 0.8rem;
    }

    .card-body {
        padding: 0.8rem !important;
    }

    .mb-3 {
        margin-bottom: 0.7rem !important;
    }

    .mb-2 {
        margin-bottom: 0.4rem !important;
    }

    .my-2 {
        margin-top: 0.4rem !important;
        margin-bottom: 0.4rem !important;
    }

    .mt-3 {
        margin-top: 0.7rem !important;
    }

    .small {
        font-size: 0.75rem !important;
    }

    .fs-6 {
        font-size: 0.9rem !important;
    }

    .text-muted.small {
        font-size: 0.7rem !important;
    }

    /* Style untuk tampilan mobile */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .row {
            margin-left: -0.25rem;
            margin-right: -0.25rem;
        }

        .col, [class*="col-"] {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .form-control, .form-select {
            padding: 0.35rem 0.5rem;
            font-size: 0.85rem;
        }

        .payment-method-item {
            padding: 0.35rem;
        }

        .payment-icon {
            font-size: 1rem;
            margin-right: 0.4rem;
        }

        .card-header {
            padding: 0.35rem 0.6rem;
        }

        .card-body {
            padding: 0.6rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row g-2">
    <div class="col-lg-7 mb-2" data-aos="fade-up" data-aos-delay="100">
        <div class="card checkout-form-card">
            <div class="card-header bg-dark text-white py-2">
                <h5 class="mb-0 fs-6">Informasi Pesanan</h5>
            </div>
            <div class="card-body">
                <form id="checkout-form" method="POST" action="{{ route('kiosk.process-order') }}">
                    @csrf
                    <input type="hidden" name="cart_items" id="cart-items-input">
                    <input type="hidden" name="total_amount" id="total-amount-input">
                    <input type="hidden" name="order_type" id="order-type-input">

                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-2 py-1 px-2" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-2">
                        <label for="customer_name" class="form-label">Nama Anda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required placeholder="Masukkan nama Anda">
                    </div>

                    <div class="mb-2">
                        <label for="notes" class="form-label">Catatan Pesanan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="1" placeholder="Tambahkan catatan untuk pesanan Anda (opsional)"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1">Tipe Pesanan</label>
                        <div class="p-1 bg-light rounded d-flex align-items-center">
                            <i id="order-type-icon" class="fas fa-utensils me-2 text-primary"></i>
                            <div>
                                <div class="fw-bold small" id="order-type-text">-</div>
                                <div class="text-muted small" id="order-type-desc">-</div>
                            </div>
                            <a href="{{ route('kiosk.order-type') }}" class="btn btn-sm btn-outline-secondary ms-auto py-1 px-2">
                                Ubah
                            </a>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-1">Metode Pembayaran <span class="text-danger">*</span></label>

                        @if(isset($paymentSettings['payment_cash_enabled']) && $paymentSettings['payment_cash_enabled'] == '1')
                        <div class="payment-method-item {{ (!isset($paymentSettings['payment_qris_enabled']) || $paymentSettings['payment_qris_enabled'] != '1') &&
                                                        (!isset($paymentSettings['payment_debit_enabled']) || $paymentSettings['payment_debit_enabled'] != '1') ? 'active' : '' }}"
                            data-payment="cash">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill-wave payment-icon"></i>
                                <div>
                                    <div class="fw-bold small">Tunai</div>
                                    <div class="text-muted small">Bayar langsung di kasir</div>
                                </div>
                                <div class="ms-auto">
                                    <input type="radio" name="payment_method" value="cash"
                                        {{ (!isset($paymentSettings['payment_qris_enabled']) || $paymentSettings['payment_qris_enabled'] != '1') &&
                                        (!isset($paymentSettings['payment_debit_enabled']) || $paymentSettings['payment_debit_enabled'] != '1') ? 'checked' : '' }}
                                        class="form-check-input">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($paymentSettings['payment_qris_enabled']) && $paymentSettings['payment_qris_enabled'] == '1')
                        <div class="payment-method-item {{ (isset($paymentSettings['payment_qris_enabled']) && $paymentSettings['payment_qris_enabled'] == '1') &&
                                                        (!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') ? 'active' : '' }}"
                            data-payment="qris">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-qrcode payment-icon"></i>
                                <div>
                                    <div class="fw-bold small">QRIS</div>
                                    <div class="text-muted small">Bayar dengan scan QR code</div>
                                </div>
                                <div class="ms-auto">
                                    <input type="radio" name="payment_method" value="qris"
                                        {{ (isset($paymentSettings['payment_qris_enabled']) && $paymentSettings['payment_qris_enabled'] == '1') &&
                                        (!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') ? 'checked' : '' }}
                                        class="form-check-input">
                                </div>
                            </div>
                        </div>

                        <!-- Container untuk menampilkan QRIS -->
                        <div id="qris-container" class="{{ (isset($paymentSettings['payment_qris_enabled']) && $paymentSettings['payment_qris_enabled'] == '1') &&
                                                        (!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') ? 'show' : '' }}">
                            @if(isset($paymentSettings['payment_qris_image']) && !empty($paymentSettings['payment_qris_image']))
                                <img src="{{ asset($paymentSettings['payment_qris_image']) }}" alt="QRIS Code" id="qris-image" onclick="openQrisModal()">
                                <p class="mb-0 text-muted small">Scan QR code di atas menggunakan aplikasi pembayaran</p>
                                <button type="button" class="btn-zoom mt-2" onclick="openQrisModal()">
                                    <i class="fas fa-search-plus me-1"></i>Perbesar QRIS
                                </button>
                            @else
                                <div class="alert alert-warning py-1 px-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Kode QRIS belum tersedia. Silakan hubungi kasir untuk bantuan.
                                </div>
                            @endif
                        </div>
                        @endif

                        @if(isset($paymentSettings['payment_debit_enabled']) && $paymentSettings['payment_debit_enabled'] == '1')
                        <div class="payment-method-item {{ (isset($paymentSettings['payment_debit_enabled']) && $paymentSettings['payment_debit_enabled'] == '1') &&
                                                        (!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') &&
                                                        (!isset($paymentSettings['payment_qris_enabled']) || $paymentSettings['payment_qris_enabled'] != '1') ? 'active' : '' }}"
                            data-payment="card">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <div>
                                    <div class="fw-bold small">Kartu Debit</div>
                                    <div class="text-muted small">
                                        Bayar dengan kartu bank
                                        @if(isset($paymentSettings['payment_debit_cards']) && !empty($paymentSettings['payment_debit_cards']))
                                        <span class="small">({{ $paymentSettings['payment_debit_cards'] }})</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ms-auto">
                                    <input type="radio" name="payment_method" value="card"
                                        {{ (isset($paymentSettings['payment_debit_enabled']) && $paymentSettings['payment_debit_enabled'] == '1') &&
                                        (!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') &&
                                        (!isset($paymentSettings['payment_qris_enabled']) || $paymentSettings['payment_qris_enabled'] != '1') ? 'checked' : '' }}
                                        class="form-check-input">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if((!isset($paymentSettings['payment_cash_enabled']) || $paymentSettings['payment_cash_enabled'] != '1') &&
                            (!isset($paymentSettings['payment_qris_enabled']) || $paymentSettings['payment_qris_enabled'] != '1') &&
                            (!isset($paymentSettings['payment_debit_enabled']) || $paymentSettings['payment_debit_enabled'] != '1'))
                            <div class="alert alert-warning py-1 px-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Tidak ada metode pembayaran yang tersedia. Silakan hubungi kasir untuk bantuan.
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="{{ route('kiosk.cart') }}" class="back-to-cart">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Keranjang
                        </a>
                        <button type="submit" id="place-order-btn" class="btn btn-primary order-btn" disabled>
                            <i class="fas fa-check-circle me-1"></i> Pesan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5" data-aos="fade-up" data-aos-delay="200">
        <div class="card order-summary-card">
            <div class="card-header bg-dark text-white py-2">
                <h5 class="mb-0 fs-6">Ringkasan Pesanan</h5>
            </div>
            <div class="card-body">
                <div id="order-items" class="mb-2">
                    <!-- Item pesanan akan ditampilkan di sini -->
                </div>

                <hr class="my-2">

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">Total Item:</span>
                    <span class="fw-bold small" id="total-items">0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">Total Harga:</span>
                    <span class="fw-bold small" id="subtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">Pajak ({{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }}%):</span>
                    <span class="fw-bold small" id="tax">Rp 0</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <span class="fs-6 fw-bold">Total Bayar:</span>
                    <span class="fs-6 fw-bold" id="total">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan QRIS yang diperbesar -->
<div id="qrisModal" class="qris-modal">
    <div class="qris-modal-content">
        <span class="qris-modal-close" onclick="closeQrisModal()">&times;</span>
        <img id="qrisModalImage" src="" alt="QRIS Code">
        <h5 class="mt-2 mb-1 fs-6">QRIS Pembayaran</h5>
        <p class="mb-1 small">Scan QR code ini menggunakan aplikasi pembayaran Anda</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderItemsContainer = document.getElementById('order-items');
        const totalItemsElement = document.getElementById('total-items');
        const subtotalElement = document.getElementById('subtotal');
        const totalElement = document.getElementById('total');
        const cartItemsInput = document.getElementById('cart-items-input');
        const totalAmountInput = document.getElementById('total-amount-input');
        const placeOrderBtn = document.getElementById('place-order-btn');

        // Fungsi untuk menampilkan dan menutup modal QRIS
        window.openQrisModal = function() {
            const modal = document.getElementById('qrisModal');
            const qrisImage = document.getElementById('qris-image');
            const modalImage = document.getElementById('qrisModalImage');

            // Ambil path gambar dari gambar QRIS utama
            if(qrisImage) {
                modalImage.src = qrisImage.src;
            }

            // Tampilkan modal
            modal.style.display = 'block';

            // Aktifkan tombol escape untuk menutup modal
            document.addEventListener('keydown', function(event) {
                if(event.key === 'Escape') {
                    closeQrisModal();
                }
            });

            // Tambahkan event listener untuk menutup modal dengan klik di luar konten
            modal.addEventListener('click', function(event) {
                if(event.target === modal) {
                    closeQrisModal();
                }
            });
        };

        window.closeQrisModal = function() {
            const modal = document.getElementById('qrisModal');
            modal.style.display = 'none';
        };

        // Ambil keranjang dari sessionStorage
        let cart = [];
        if (sessionStorage.getItem('cart')) {
            try {
                cart = JSON.parse(sessionStorage.getItem('cart'));
            } catch (e) {
                console.error('Error parsing cart data from sessionStorage', e);
                sessionStorage.removeItem('cart');
                window.location.href = "{{ route('kiosk.cart') }}";
            }
        }

        // Tampilkan item pesanan
        function renderOrderItems() {
            // Bersihkan kontainer
            orderItemsContainer.innerHTML = '';

            if (cart.length === 0) {
                // Redirect ke halaman keranjang jika kosong
                window.location.href = "{{ route('kiosk.cart') }}";
                return;
            }

            // Tambahkan item ke daftar
            cart.forEach((item, index) => {
                const subtotal = item.price * item.quantity;

                const orderItem = document.createElement('div');
                orderItem.className = 'order-summary-item';
                orderItem.setAttribute('data-aos', 'fade-up');
                orderItem.setAttribute('data-aos-delay', index * 50);

                // Tambahkan badge varian jika tersedia
                let variantBadge = '';
                if (item.variantType === 'hot') {
                    variantBadge = `<span class="variant-badge variant-hot"><i class="fas fa-mug-hot fa-xs"></i> Panas</span>`;
                } else if (item.variantType === 'ice') {
                    variantBadge = `<span class="variant-badge variant-ice"><i class="fas fa-cube fa-xs"></i> Dingin</span>`;
                }

                orderItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold small">${item.name}</span>
                                ${variantBadge}
                            </div>
                            <div class="text-muted" style="font-size: 0.7rem;">${item.quantity} x Rp ${formatNumber(item.price)}</div>
                        </div>
                        <div class="fw-bold small">Rp ${formatNumber(subtotal)}</div>
                    </div>
                `;

                orderItemsContainer.appendChild(orderItem);
            });

            // Perbarui total
            updateOrderSummary();

            // Aktifkan tombol pesan
            placeOrderBtn.disabled = false;
        }

        // Format angka ke format rupiah
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Format ke format mata uang rupiah
        function formatRupiah(number) {
            return 'Rp ' + formatNumber(number);
        }

        // Perbarui ringkasan pesanan
        function updateOrderSummary() {
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

            // Hitung pajak berdasarkan pengaturan
            const taxPercentage = {{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }} / 100;
            const tax = subtotal * taxPercentage;
            const total = subtotal + tax;

            totalItemsElement.textContent = totalItems;
            subtotalElement.textContent = `Rp ${formatNumber(subtotal)}`;
            document.getElementById('tax').textContent = `Rp ${formatNumber(tax)}`;
            totalElement.textContent = `Rp ${formatNumber(total)}`;

            // Set nilai input tersembunyi untuk form
            cartItemsInput.value = JSON.stringify(cart);
            totalAmountInput.value = total; // Ubah ke total termasuk pajak
        }

        // Handle klik pada metode pembayaran
        const paymentMethods = document.querySelectorAll('.payment-method-item');
        const qrisContainer = document.getElementById('qris-container');

        if (paymentMethods.length > 0) {
            // Jika ada metode pembayaran yang tersedia
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Hapus kelas active dari semua metode
                    paymentMethods.forEach(m => m.classList.remove('active'));

                    // Tambahkan kelas active ke metode yang diklik
                    this.classList.add('active');

                    // Set radio button terkait
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;

                    // Cek apakah metode pembayaran QRIS yang dipilih
                    const paymentType = this.getAttribute('data-payment');
                    if (paymentType === 'qris' && qrisContainer) {
                        qrisContainer.classList.add('show');
                    } else if (qrisContainer) {
                        qrisContainer.classList.remove('show');
                    }

                    // Efek klik visual
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });

            // Pastikan salah satu metode pembayaran tercentang saat halaman dimuat
            const anyChecked = Array.from(document.querySelectorAll('input[name="payment_method"]:checked')).length > 0;

            if (!anyChecked && paymentMethods.length > 0) {
                // Pilih metode pembayaran pertama jika tidak ada yang tercentang
                const firstMethod = paymentMethods[0];
                firstMethod.classList.add('active');
                const firstRadio = firstMethod.querySelector('input[type="radio"]');
                firstRadio.checked = true;

                // Cek jika metode pertama adalah QRIS
                const paymentType = firstMethod.getAttribute('data-payment');
                if (paymentType === 'qris' && qrisContainer) {
                    qrisContainer.classList.add('show');
                } else if (qrisContainer) {
                    qrisContainer.classList.remove('show');
                }
            } else {
                // Jika sudah ada yang tercentang, periksa apakah itu QRIS
                const selectedMethod = document.querySelector('.payment-method-item.active');
                if (selectedMethod) {
                    const paymentType = selectedMethod.getAttribute('data-payment');
                    if (paymentType === 'qris' && qrisContainer) {
                        qrisContainer.classList.add('show');
                    } else if (qrisContainer) {
                        qrisContainer.classList.remove('show');
                    }
                }
            }

            // Aktifkan tombol pesan hanya jika metode pembayaran tersedia
            placeOrderBtn.disabled = false;
        } else {
            // Jika tidak ada metode pembayaran yang tersedia
            placeOrderBtn.disabled = true;
            placeOrderBtn.textContent = 'Tidak Ada Metode Pembayaran';
        }

        // Submit form
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            // Validasi jika keranjang kosong
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang Anda kosong. Silahkan tambahkan produk terlebih dahulu.');
                window.location.href = "{{ route('kiosk.index') }}";
            }

            // Simpan data form ke sessionStorage untuk digunakan jika halaman di-refresh
            const customerName = document.getElementById('customer_name').value;
            const notes = document.getElementById('notes').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            sessionStorage.setItem('checkout_data', JSON.stringify({
                customer_name: customerName,
                notes: notes,
                payment_method: paymentMethod
            }));
        });

        // Cek apakah ada data checkout yang tersimpan
        if (sessionStorage.getItem('checkout_data')) {
            try {
                const checkoutData = JSON.parse(sessionStorage.getItem('checkout_data'));
                if (checkoutData.customer_name) {
                    document.getElementById('customer_name').value = checkoutData.customer_name;
                }
                if (checkoutData.notes) {
                    document.getElementById('notes').value = checkoutData.notes;
                }
                if (checkoutData.payment_method) {
                    // Cek apakah metode pembayaran masih tersedia
                    const radio = document.querySelector(`input[name="payment_method"][value="${checkoutData.payment_method}"]`);
                    if (radio) {
                        radio.checked = true;
                        // Aktifkan item pembayaran yang sesuai
                        const paymentItem = radio.closest('.payment-method-item');
                        if (paymentItem) {
                            document.querySelectorAll('.payment-method-item').forEach(m => m.classList.remove('active'));
                            paymentItem.classList.add('active');

                            // Cek apakah metode pembayaran QRIS yang dipilih
                            const paymentType = paymentItem.getAttribute('data-payment');
                            if (paymentType === 'qris' && qrisContainer) {
                                qrisContainer.classList.add('show');
                            } else if (qrisContainer) {
                                qrisContainer.classList.remove('show');
                            }
                        }
                    } else {
                        // Jika metode pembayaran sebelumnya tidak tersedia lagi, pilih yang pertama
                        if (paymentMethods.length > 0) {
                            paymentMethods[0].classList.add('active');
                            const firstRadio = paymentMethods[0].querySelector('input[type="radio"]');
                            if (firstRadio) {
                                firstRadio.checked = true;

                                // Cek apakah metode pertama adalah QRIS
                                const paymentType = paymentMethods[0].getAttribute('data-payment');
                                if (paymentType === 'qris' && qrisContainer) {
                                    qrisContainer.classList.add('show');
                                } else if (qrisContainer) {
                                    qrisContainer.classList.remove('show');
                                }
                            }
                        }
                    }
                }
            } catch (e) {
                console.error('Error parsing checkout data from sessionStorage', e);
                sessionStorage.removeItem('checkout_data');
            }
        }

        // Tambahkan event listener untuk perubahan di sessionStorage
        window.addEventListener('storage', function(e) {
            if (e.key === 'cart') {
                // Reload halaman jika keranjang berubah
                location.reload();
            }
        });

        // Set tipe pesanan dari session storage atau dari session PHP
        const orderType = sessionStorage.getItem('orderType') || '{{ session('order_type', 'dine-in') }}';
        const orderTypeInput = document.getElementById('order-type-input');
        const orderTypeText = document.getElementById('order-type-text');
        const orderTypeDesc = document.getElementById('order-type-desc');
        const orderTypeIcon = document.getElementById('order-type-icon');

        orderTypeInput.value = orderType;

        if (orderType === 'dine-in') {
            orderTypeText.textContent = 'Dine In';
            orderTypeDesc.textContent = 'Makan di tempat';
            orderTypeIcon.className = 'fas fa-utensils me-2 fs-5 text-primary';
        } else {
            orderTypeText.textContent = 'Take Away';
            orderTypeDesc.textContent = 'Bawa pulang';
            orderTypeIcon.className = 'fas fa-shopping-bag me-2 fs-5 text-primary';
        }

        // Render item pesanan saat halaman dimuat
        renderOrderItems();
    });
</script>
@endsection

