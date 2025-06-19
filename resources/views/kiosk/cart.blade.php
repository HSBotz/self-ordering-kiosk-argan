@extends('layouts.app')

@section('styles')
<style>
    .cart-header {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 0.8rem 0;
        margin-bottom: 1rem;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .cart-summary-card {
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 80px;
    }

    .cart-card {
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .cart-item {
        border-left: 3px solid var(--primary-color);
        transition: all 0.3s;
        padding: 0.6rem !important;
        margin-bottom: 0.5rem !important;
    }

    .cart-item:hover {
        background-color: rgba(0,0,0,0.02);
    }

    .quantity-control {
        max-width: 100px;
        margin: 0 auto;
    }

    .checkout-btn {
        padding: 8px;
        font-weight: 600;
    }

    .clear-cart-btn {
        border: 1px solid #dc3545;
        color: #dc3545;
        font-weight: 500;
        padding: 6px;
    }

    .clear-cart-btn:hover {
        background-color: #dc3545;
        color: white;
    }

    .item-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    .empty-cart {
        padding: 1.5rem 0;
    }

    .empty-cart i {
        font-size: 3rem;
        color: var(--secondary-color);
        opacity: 0.4;
    }

    .table > :not(caption) > * > * {
        padding: 0.6rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .summary-item.total {
        font-size: 1.1rem;
        font-weight: 600;
        margin-top: 0.4rem;
    }

    .summary-label {
        color: #555;
    }

    .summary-value {
        font-weight: 500;
    }

    .variant-badge {
        font-size: 0.65rem;
        padding: 0.1rem 0.3rem;
        border-radius: 3px;
        margin-left: 0.3rem;
        display: inline-block;
        vertical-align: middle;
    }

    .variant-hot {
        background-color: #ffcccb;
        color: #d63031;
    }

    .variant-ice {
        background-color: #c7ecee;
        color: #0984e3;
    }

    .card-header {
        padding: 0.5rem 1rem;
    }

    .card-body {
        padding: 0.8rem;
    }

    h5 {
        font-size: 1rem;
        margin-bottom: 0.3rem !important;
    }

    .text-muted {
        font-size: 0.85rem;
    }

    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    .d-grid.gap-3 {
        gap: 0.5rem !important;
    }

    .mb-4 {
        margin-bottom: 1rem !important;
    }

    .cart-header h2 {
        font-size: 1.5rem;
        margin-bottom: 0.3rem !important;
    }

    .cart-header p {
        font-size: 0.9rem;
        margin-bottom: 0.3rem !important;
    }

    /* Optimasi untuk layar kecil */
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

        .cart-header {
            padding: 0.5rem 0;
        }

        .cart-header h2 {
            font-size: 1.3rem;
        }

        .cart-header p {
            font-size: 0.8rem;
        }

        .item-img {
            width: 40px;
            height: 40px;
        }

        h5 {
            font-size: 0.9rem;
        }

        .text-muted, .small {
            font-size: 0.75rem;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.1rem 0.3rem;
            font-size: 0.75rem;
        }

        .input-group-sm > .btn {
            padding: 0.1rem 0.3rem;
        }

        .card-header {
            padding: 0.4rem 0.6rem;
        }

        .card-body {
            padding: 0.6rem;
        }
    }
</style>
@endsection

@section('content')
<div class="cart-header" data-aos="fade-up">
    <h2 class="mb-1"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h2>
    <p>Lihat dan sesuaikan item pesanan Anda</p>
</div>

<div class="row g-2">
    <div class="col-lg-8 mb-2" data-aos="fade-up" data-aos-delay="100">
        <div class="card cart-card">
            <div class="card-header bg-dark text-white py-2">
                <h5 class="mb-0">Item Pesanan</h5>
            </div>
            <div class="card-body">
                <div id="cart-items">
                    <!-- Item keranjang akan ditampilkan di sini -->
                </div>

                <div id="empty-cart-message" class="text-center empty-cart">
                    <i class="fas fa-shopping-basket mb-3"></i>
                    <h4>Keranjang Anda Kosong</h4>
                    <p class="text-muted mb-3">Silahkan tambahkan beberapa produk ke keranjang Anda</p>
                    <a href="{{ route('kiosk.index') }}" class="btn btn-primary">
                        <i class="fas fa-coffee me-2"></i> Lihat Menu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card cart-summary-card">
            <div class="card-header bg-dark text-white py-2">
                <h5 class="mb-0">Ringkasan Pesanan</h5>
            </div>
            <div class="card-body">
                <div class="summary-item">
                    <span class="summary-label">Total Item:</span>
                    <span class="summary-value" id="total-items">0</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Harga:</span>
                    <span class="summary-value" id="subtotal">Rp 0</span>
                </div>
                <div class="summary-item" id="tax-container">
                    <span class="summary-label">Pajak ({{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }}%):</span>
                    <span class="summary-value" id="tax">Rp 0</span>
                </div>
                <hr class="my-2">
                <div class="summary-item total">
                    <span>Total Bayar:</span>
                    <span id="total">Rp 0</span>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button id="checkout-btn" class="btn btn-primary checkout-btn" disabled>
                        <i class="fas fa-credit-card me-1"></i> Lanjutkan ke Pembayaran
                    </button>
                    <button id="clear-cart-btn" class="btn clear-cart-btn" disabled>
                        <i class="fas fa-trash me-1"></i> Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const totalItemsElement = document.getElementById('total-items');
        const subtotalElement = document.getElementById('subtotal');
        const taxElement = document.getElementById('tax');
        const totalElement = document.getElementById('total');
        const checkoutBtn = document.getElementById('checkout-btn');
        const clearCartBtn = document.getElementById('clear-cart-btn');

        // Ambil keranjang dari sessionStorage
        let cart = [];
        if (sessionStorage.getItem('cart')) {
            try {
                cart = JSON.parse(sessionStorage.getItem('cart'));
            } catch (e) {
                console.error('Error parsing cart data from sessionStorage', e);
                sessionStorage.removeItem('cart');
            }
        }

        // Tampilkan item di keranjang
        function renderCart() {
            // Bersihkan kontainer
            cartItemsContainer.innerHTML = '';

            if (cart.length === 0) {
                // Tampilkan pesan keranjang kosong
                emptyCartMessage.style.display = 'block';
                checkoutBtn.disabled = true;
                clearCartBtn.disabled = true;
            } else {
                // Sembunyikan pesan keranjang kosong
                emptyCartMessage.style.display = 'none';
                checkoutBtn.disabled = false;
                clearCartBtn.disabled = false;

                // Buat wadah untuk item
                const cartItemsList = document.createElement('div');
                cartItemsList.className = 'cart-items-list';

                // Tambahkan item ke daftar
                cart.forEach((item, index) => {
                    const subtotal = item.price * item.quantity;

                    const cartItem = document.createElement('div');
                    cartItem.className = 'cart-item p-3 mb-3 rounded';
                    cartItem.setAttribute('data-aos', 'fade-up');
                    cartItem.setAttribute('data-aos-delay', index * 50);

                    // Coba dapatkan gambar produk dari database jika tersedia
                    let imgSrc = '';

                    // Gunakan URL gambar dari item keranjang jika tersedia
                    if (item.imageUrl) {
                        imgSrc = item.imageUrl;
                    } else {
                        // Jika tidak ada gambar yang ditemukan, gunakan placeholder
                        imgSrc = `https://source.unsplash.com/100x100/?coffee,${encodeURIComponent(item.name)}`;
                    }

                    cartItem.innerHTML = `
                        <div class="row align-items-center g-1">
                            <div class="col-md-2 col-3 mb-1 mb-md-0">
                                <img src="${imgSrc}" class="item-img" alt="${item.name}">
                            </div>
                            <div class="col-md-4 col-9 mb-1 mb-md-0">
                                <h5 class="mb-0">
                                    ${item.originalName || item.name}
                                    ${item.variantType === 'hot' ?
                                      '<span class="variant-badge variant-hot"><i class="fas fa-mug-hot"></i> Panas</span>' :
                                      item.variantType === 'ice' ?
                                      '<span class="variant-badge variant-ice"><i class="fas fa-cube"></i> Dingin</span>' :
                                      ''}
                                </h5>
                                <div class="text-muted small">Rp ${formatNumber(item.price)}</div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="input-group input-group-sm quantity-control">
                                    <button class="btn btn-outline-secondary btn-sm decrease-qty" data-id="${item.id}" data-variant="${item.variantType || ''}">-</button>
                                    <input type="text" class="form-control form-control-sm text-center item-qty" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary btn-sm increase-qty" data-id="${item.id}" data-variant="${item.variantType || ''}">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 text-end">
                                <div class="fw-bold small">Rp ${formatNumber(subtotal)}</div>
                            </div>
                            <div class="col-md-1 col-2 text-end">
                                <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}" data-variant="${item.variantType || ''}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;

                    cartItemsList.appendChild(cartItem);
                });

                cartItemsContainer.appendChild(cartItemsList);

                // Perbarui total
                updateCartSummary();

                // Tambahkan event listener untuk tombol
                addCartButtonListeners();
            }
        }

        // Format angka ke format rupiah
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Perbarui ringkasan keranjang
        function updateCartSummary() {
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

            // Gunakan nilai pajak dari pengaturan
            const taxPercentage = {{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }} / 100;
            const tax = subtotal * taxPercentage;
            const total = subtotal + tax;

            totalItemsElement.textContent = totalItems;
            subtotalElement.textContent = `Rp ${formatNumber(subtotal)}`;
            taxElement.textContent = `Rp ${formatNumber(tax)}`;
            totalElement.textContent = `Rp ${formatNumber(total)}`;
        }

        // Tambahkan event listener untuk tombol di keranjang
        function addCartButtonListeners() {
            // Tombol kurangi quantity
            document.querySelectorAll('.decrease-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;

                    // Cari item berdasarkan id dan variantType
                    const item = cart.find(item =>
                        item.id === productId &&
                        (variantType ? item.variantType === variantType : !item.variantType)
                    );

                    if (item && item.quantity > 1) {
                        item.quantity -= 1;
                        saveCart();
                        renderCart();
                    }
                });
            });

            // Tombol tambah quantity
            document.querySelectorAll('.increase-qty').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;

                    // Cari item berdasarkan id dan variantType
                    const item = cart.find(item =>
                        item.id === productId &&
                        (variantType ? item.variantType === variantType : !item.variantType)
                    );

                    if (item) {
                        item.quantity += 1;
                        saveCart();
                        renderCart();
                    }
                });
            });

            // Tombol hapus item
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;

                    // Hapus item dari keranjang berdasarkan id dan variantType
                    cart = cart.filter(item =>
                        !(item.id === productId &&
                        (variantType ? item.variantType === variantType : !item.variantType))
                    );

                    saveCart();
                    renderCart();

                    // Tampilkan notifikasi
                    showNotification('Item berhasil dihapus dari keranjang');
                });
            });
        }

        // Simpan keranjang ke sessionStorage
        function saveCart() {
            sessionStorage.setItem('cart', JSON.stringify(cart));

            // Trigger storage event untuk halaman lain
            if (typeof window.dispatchEvent === 'function') {
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'cart'
                }));
            }

            updateCartCount();
        }

        // Event listener untuk tombol checkout
        checkoutBtn.addEventListener('click', function() {
            window.location.href = "{{ route('kiosk.checkout') }}";
        });

        // Event listener untuk tombol kosongkan keranjang
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                cart = [];
                saveCart();
                renderCart();

                // Tampilkan notifikasi
                showNotification('Keranjang berhasil dikosongkan');
            }
        });

        // Tampilkan notifikasi
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'toast align-items-center text-white bg-success';
            notification.setAttribute('role', 'alert');
            notification.setAttribute('aria-live', 'assertive');
            notification.setAttribute('aria-atomic', 'true');
            notification.style.position = 'fixed';
            notification.style.bottom = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';

            notification.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            document.body.appendChild(notification);
            const toast = new bootstrap.Toast(notification, { delay: 3000 });
            toast.show();

            setTimeout(() => {
                notification.remove();
            }, 3500);
        }

        // Update cart count in the header
        function updateCartCount() {
            const cartBadge = document.getElementById('cart-count');
            if (cartBadge) {
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                cartBadge.textContent = totalItems;

                if (totalItems > 0) {
                    cartBadge.style.display = 'inline-block';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        }

        // Render keranjang saat halaman dimuat
        renderCart();

        // Tambahkan event listener untuk perubahan di sessionStorage
        window.addEventListener('storage', function(e) {
            if (e.key === 'cart') {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart')) || [];
                    renderCart();
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                }
            }
        });
    });
</script>
@endsection
