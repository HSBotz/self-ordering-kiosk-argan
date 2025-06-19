@extends('layouts.app')

@section('content')
<div class="row g-2">
    <div class="col-lg-9">
        <div class="categories-section mb-2" data-aos="fade-up">
            <h3 class="text-center mb-1 fs-6">Kategori Menu</h3>
            <div class="d-flex flex-wrap gap-1 justify-content-center mb-1">
                <div class="category-pill all-category active">
                    <i class="fas fa-th-large me-1"></i>Semua Menu
                </div>
                @foreach($categories as $category)
                <div class="category-pill" data-category="{{ $category->id }}">
                    @if($category->icon)
                        <i class="{{ $category->icon }} me-1"></i>
                    @elseif($category->name == 'Kopi Panas')
                        <i class="fas fa-mug-hot me-1"></i>
                    @elseif($category->name == 'Kopi Dingin')
                        <i class="fas fa-glass-whiskey me-1"></i>
                    @elseif($category->name == 'Non-Kopi')
                        <i class="fas fa-blender me-1"></i>
                    @elseif($category->name == 'Makanan')
                        <i class="fas fa-utensils me-1"></i>
                    @else
                        <i class="fas fa-cookie me-1"></i>
                    @endif
                    {{ $category->name }}
                </div>
                @endforeach
            </div>
        </div>

        <div class="products-section">
            <h3 class="text-center mb-1 fs-6" data-aos="fade-up">Menu Kami</h3>
            <div class="row product-grid g-1" id="products-container">
                @foreach($products as $product)
                <div class="col-md-6 col-lg-4 mb-2 product-item"
                    data-category="{{ $product->category_id }}"
                    data-category-name="{{ $product->category->name }}"
                    data-category-icon="{{ $product->category->icon ?? '' }}"
                    data-has-variants="{{ $product->category->has_variants ? 'true' : 'false' }}"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    <div class="card h-100">
                        <div class="position-relative">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="https://source.unsplash.com/480x360/?coffee,{{ Str::slug($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                            @endif
                            <div class="position-absolute top-0 end-0 m-1">
                                <span class="price-tag">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="card-body p-2 d-flex flex-column">
                            <h5 class="card-title fs-6 mb-1">{{ $product->name }}</h5>
                            <p class="card-text text-muted small">{{ $product->description }}</p>
                            <button class="btn btn-primary btn-sm add-to-cart mt-auto"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->price }}">
                                <i class="fas fa-cart-plus me-1"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div class="col-lg-3">
        <div class="cart-sidebar" id="cartSidebar">
            <div class="cart-header p-2">
                <h4 class="fs-5 mb-0"><i class="fas fa-shopping-cart me-1"></i>Keranjang</h4>
                <button class="btn-close-cart d-lg-none" id="closeCart"><i class="fas fa-times"></i></button>
            </div>
            <div class="cart-body" id="cartItems">
                <div class="text-center py-3 empty-cart-message">
                    <i class="fas fa-shopping-basket mb-2"></i>
                    <p class="mb-1">Keranjang Anda masih kosong</p>
                    <small class="text-muted">Tambahkan menu favorit Anda</small>
                </div>
                <!-- Cart items will be displayed here -->
            </div>
            <div class="cart-footer p-2">
                <div class="cart-summary mb-2">
                    <div class="cart-summary-row small">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">Rp 0</span>
                    </div>
                    <div class="cart-summary-row small">
                        <span>Pajak ({{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }}%):</span>
                        <span id="cartTax">Rp 0</span>
                    </div>
                    <div class="cart-summary-row total">
                        <span>Total:</span>
                        <span id="cartTotal">Rp 0</span>
                    </div>
                </div>
                <a href="{{ route('kiosk.cart') }}" class="checkout-btn py-1" id="checkoutBtn" disabled>
                    <i class="fas fa-shopping-cart me-1"></i> Checkout
                </a>
            </div>
        </div>
        <div class="cart-toggle-btn d-lg-none" id="showCartBtn">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-toggle-badge" id="cartToggleBadge">0</span>
        </div>
    </div>
</div>

<!-- Modal Product Detail -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" id="productDetailModalLabel">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2">
                <div class="row g-2">
                    <div class="col-md-6">
                        <img src="" id="modalProductImage" class="img-fluid rounded" alt="Product Image">
                    </div>
                    <div class="col-md-6">
                        <h3 id="modalProductName" class="fs-5 mb-1"></h3>
                        <p class="text-muted small mb-1" id="modalProductCategory">
                            <i id="modalCategoryIcon" class="me-1"></i>
                            <span id="modalCategoryName"></span>
                        </p>
                        <h4 class="price-tag my-1" id="modalProductPrice"></h4>
                        <div class="mt-2 mb-3">
                            <h6 class="small fw-bold mb-1">Deskripsi:</h6>
                            <p id="modalProductDescription" class="small p-2" style="overflow-wrap: break-word; max-height: 150px; overflow-y: auto;"></p>
                        </div>

                        <!-- Opsi Varian Panas/Dingin -->
                        <div id="variantOptions" class="mb-3" style="display: none;">
                            <h6 class="small fw-bold mb-1">Pilihan:</h6>
                            <div class="btn-group w-100" role="group" aria-label="Pilihan Panas/Dingin">
                                <input type="radio" class="btn-check" name="variant_type" id="hot" value="hot" autocomplete="off" checked>
                                <label class="btn btn-outline-danger btn-sm" for="hot">
                                    <i class="fas fa-mug-hot me-1"></i> Panas
                                </label>

                                <input type="radio" class="btn-check" name="variant_type" id="ice" value="ice" autocomplete="off">
                                <label class="btn btn-outline-info btn-sm" for="ice">
                                    <i class="fas fa-cube me-1"></i> Dingin
                                </label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mt-2">
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary btn-sm" id="decreaseQuantity" type="button">-</button>
                                <input type="text" class="form-control form-control-sm text-center" id="productQuantity" value="1" readonly>
                                <button class="btn btn-outline-secondary btn-sm" id="increaseQuantity" type="button">+</button>
                            </div>
                            <button class="btn btn-primary btn-sm ms-2" id="modalAddToCart">
                                <i class="fas fa-cart-plus me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Cart sidebar styles */
    .cart-sidebar {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 0;
        position: sticky;
        top: 60px; /* Mengurangi jarak dari atas */
        max-height: calc(100vh - 70px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Styling untuk kartu produk */
    .product-item .card {
        height: 100%;
        transition: transform 0.3s ease;
    }

    .product-item .card:hover {
        transform: translateY(-5px);
    }

    .product-item .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-item .card-text {
        overflow-y: auto;
        max-height: 80px;
        margin-bottom: 10px;
        font-size: 0.8rem;
    }

    /* Memastikan tombol di bagian bawah */
    .product-item .add-to-cart {
        margin-top: auto;
    }

    /* Style untuk deskripsi di modal */
    #modalProductDescription {
        line-height: 1.5;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .cart-header {
        background-color: var(--primary-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-header h4 {
        margin: 0;
        font-weight: 600;
    }

    .btn-close-cart {
        background: none;
        border: none;
        color: white;
        font-size: 1rem;
    }

    .cart-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(100vh - 200px); /* Mengurangi tinggi maksimum */
        padding: 0;
    }

    .cart-item {
        display: flex;
        padding: 8px;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
    }

    /* Memperbaiki tampilan gambar dalam cart */
    .cart-item-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        overflow: hidden;
        margin-right: 10px;
        background-color: #f8f9fa;
        border: 1px solid #eee;
        flex-shrink: 0;
    }

    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-item-info {
        flex-grow: 1;
        width: calc(100% - 60px);
    }

    .cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 3px;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cart-item-price {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.85rem;
        display: block;
        margin-bottom: 5px;
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .cart-item-quantity {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 2px;
    }

    .cart-item-quantity button {
        width: 24px;
        height: 24px;
        border: none;
        background: none;
        color: var(--primary-color);
        font-size: 0.9rem;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .cart-item-quantity span {
        margin: 0 6px;
        font-size: 0.9rem;
        font-weight: 500;
        min-width: 16px;
        text-align: center;
    }

    .cart-item-remove {
        color: #dc3545;
        background: none;
        border: none;
        font-size: 1rem;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .cart-item-remove:hover {
        background-color: #fff0f0;
    }

    .cart-footer {
        background-color: #f9f9f9;
        border-top: 1px solid #f0f0f0;
    }

    .cart-summary {
        font-size: 0.9rem;
    }

    .cart-summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .cart-summary-row.total {
        font-weight: 700;
        font-size: 1rem;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e0e0e0;
    }

    .checkout-btn {
        display: block;
        background-color: var(--primary-color);
        color: white;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
        border-radius: 8px;
    }

    .checkout-btn:hover {
        background-color: var(--secondary-color);
        color: white;
    }

    .checkout-btn[disabled] {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .empty-cart-message {
        color: #999;
    }

    .empty-cart-message i {
        font-size: 2rem;
        color: #bbb;
        display: block;
    }

    /* Mobile cart toggle button */
    .cart-toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.3rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        z-index: 1000;
    }

    .cart-toggle-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: var(--accent-color);
        color: var(--primary-color);
        font-weight: 700;
        border-radius: 50%;
        padding: 0.1rem 0.4rem;
        font-size: 0.7rem;
    }

    /* Mobile cart display */
    @media (max-width: 991.98px) {
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            bottom: 0;
            width: 85%;
            max-width: 350px;
            height: 100vh;
            max-height: 100vh;
            z-index: 1050;
            transition: right 0.3s ease;
        }

        .cart-sidebar.show {
            right: 0;
        }

        .cart-body {
            max-height: calc(100vh - 150px);
        }
    }

    /* Make the grid view more compact */
    .product-grid {
        margin-left: -0.25rem;
        margin-right: -0.25rem;
    }

    .product-grid .col-md-6, .product-grid .col-lg-4 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }

    /* Perbaikan tampilan kartu produk dan gambar */
    .card-img-top {
        height: 110px !important;
        object-fit: cover;
        object-position: center;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    /* Kategori pill */
    .category-pill {
        padding: 0.25rem 0.6rem;
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }

    /* Price tag */
    .price-tag {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
    }

    /* Notification style */
    .notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: var(--primary-color);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        z-index: 1100;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .notification.show {
        opacity: 1;
        transform: translateY(0);
    }

    .notification-content {
        display: flex;
        align-items: center;
    }

    .notification-icon {
        color: var(--accent-color);
        font-size: 1rem;
        margin-right: 8px;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
        // Filter produk berdasarkan kategori
        const categoryPills = document.querySelectorAll('.category-pill');
        const productItems = document.querySelectorAll('.product-item');

        categoryPills.forEach(pill => {
            pill.addEventListener('click', function() {
                // Hapus kelas active dari semua pill
                categoryPills.forEach(p => p.classList.remove('active'));

                // Tambahkan kelas active ke pill yang diklik
                this.classList.add('active');

                const categoryId = this.dataset.category;

                if (this.classList.contains('all-category')) {
                    // Tampilkan semua produk dengan animasi
                    productItems.forEach(item => {
                        item.style.display = 'block';
                        item.classList.add('fade-in');
                        setTimeout(() => {
                            item.classList.remove('fade-in');
                        }, 500);
                    });
                } else {
                    // Tampilkan produk berdasarkan kategori dengan animasi
                    productItems.forEach(item => {
                        if (item.dataset.category === categoryId) {
                            item.style.display = 'block';
                            item.classList.add('fade-in');
                            setTimeout(() => {
                                item.classList.remove('fade-in');
                            }, 500);
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Setup untuk modal produk
        const productDetailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        const modalProductName = document.getElementById('modalProductName');
        const modalProductImage = document.getElementById('modalProductImage');
        const modalProductPrice = document.getElementById('modalProductPrice');
        const modalProductDescription = document.getElementById('modalProductDescription');
        const modalCategoryName = document.getElementById('modalCategoryName');
        const modalCategoryIcon = document.getElementById('modalCategoryIcon');
        const productQuantity = document.getElementById('productQuantity');
        const increaseQuantity = document.getElementById('increaseQuantity');
        const decreaseQuantity = document.getElementById('decreaseQuantity');
        const modalAddToCart = document.getElementById('modalAddToCart');
        const variantOptions = document.getElementById('variantOptions');

        let currentProduct = null;
        let hasVariants = false;

        // Buka modal saat card diklik
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Jika yang diklik adalah tombol tambah ke keranjang, jangan buka modal
                if (e.target.closest('.add-to-cart')) {
                    return;
                }

                const productItem = this.closest('.product-item');
                const id = productItem.querySelector('.add-to-cart').dataset.id;
                const name = productItem.querySelector('.add-to-cart').dataset.name;
                const price = productItem.querySelector('.add-to-cart').dataset.price;
                const description = productItem.querySelector('.card-text').textContent;
                const image = productItem.querySelector('.card-img-top').src;
                const categoryName = productItem.dataset.categoryName;
                const categoryIcon = productItem.dataset.categoryIcon;
                hasVariants = productItem.dataset.hasVariants === 'true';

                currentProduct = { id: parseInt(id), name, price: parseFloat(price) };

                modalProductName.textContent = name;
                modalProductImage.src = image;
                modalProductPrice.textContent = `Rp ${formatNumber(price)}`;
                modalProductDescription.textContent = description;
                modalCategoryName.textContent = categoryName;

                // Tampilkan atau sembunyikan opsi varian berdasarkan kategori
                if (hasVariants) {
                    variantOptions.style.display = 'block';
                    // Reset pilihan ke default (panas)
                    document.getElementById('hot').checked = true;
                } else {
                    variantOptions.style.display = 'none';
                }

                // Set icon kategori
                if (categoryIcon) {
                    modalCategoryIcon.className = categoryIcon + ' me-2';
                } else {
                    // Default icon berdasarkan nama kategori
                    if (categoryName === 'Kopi Panas') {
                        modalCategoryIcon.className = 'fas fa-mug-hot me-2';
                    } else if (categoryName === 'Kopi Dingin') {
                        modalCategoryIcon.className = 'fas fa-glass-whiskey me-2';
                    } else if (categoryName === 'Non-Kopi') {
                        modalCategoryIcon.className = 'fas fa-blender me-2';
                    } else if (categoryName === 'Makanan') {
                        modalCategoryIcon.className = 'fas fa-utensils me-2';
                    } else {
                        modalCategoryIcon.className = 'fas fa-cookie me-2';
                    }
                }

                productQuantity.value = 1;

                productDetailModal.show();
            });
        });

        // Tombol tambah/kurang quantity di modal
        increaseQuantity.addEventListener('click', function() {
            productQuantity.value = parseInt(productQuantity.value) + 1;
        });

        decreaseQuantity.addEventListener('click', function() {
            if (parseInt(productQuantity.value) > 1) {
                productQuantity.value = parseInt(productQuantity.value) - 1;
            }
        });

        // Tambah ke keranjang dari modal
        modalAddToCart.addEventListener('click', function() {
            if (currentProduct) {
                // Ambil tipe varian jika ada
                let variantType = null;
                if (hasVariants) {
                    variantType = document.querySelector('input[name="variant_type"]:checked').value;
                }

                addToCart(
                    currentProduct.id,
                    currentProduct.name,
                    currentProduct.price,
                    parseInt(productQuantity.value),
                    variantType
                );

                productDetailModal.hide();

                // Notifikasi berhasil
                showNotification(`${currentProduct.name} telah ditambahkan ke keranjang!`);
            }
        });

        // Tambahkan produk ke keranjang
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const productId = parseInt(this.dataset.id);
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);
                const productItem = this.closest('.product-item');
                const hasVariants = productItem.dataset.hasVariants === 'true';

                // Jika produk memiliki varian, buka modal untuk memilih
                if (hasVariants) {
                    // Trigger click pada card untuk membuka modal
                    productItem.querySelector('.card').click();
                    return;
                }

                addToCart(productId, productName, productPrice, 1, null);

                // Notifikasi berhasil
                showNotification(`${productName} telah ditambahkan ke keranjang!`);
            });
        });

        // Fungsi untuk menambahkan produk ke keranjang
        function addToCart(id, name, price, quantity, variantType) {
            // Ambil keranjang dari sessionStorage
            let cart = [];
            if (sessionStorage.getItem('cart')) {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart'));
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                    sessionStorage.removeItem('cart');
                    cart = [];
                }
            }

            // Buat unique ID untuk item berdasarkan product ID dan variant type
            const itemId = variantType ? `${id}-${variantType}` : `${id}`;

            // Cek apakah produk sudah ada di keranjang
            const existingItem = cart.find(item =>
                (variantType ? item.id === id && item.variantType === variantType : item.id === id && !item.variantType)
            );

            // Dapatkan URL gambar produk
            let imageUrl = '';
            const productItem = Array.from(document.querySelectorAll('.product-item')).find(product => {
                return parseInt(product.querySelector('.add-to-cart').dataset.id) === id;
            });

            if (productItem) {
                const imgElement = productItem.querySelector('.card-img-top');
                if (imgElement) {
                    imageUrl = imgElement.src;
                }
            }

            // Tambahkan label varian ke nama produk jika ada
            let displayName = name;
            if (variantType) {
                displayName = variantType === 'hot' ? `${name} (Panas)` : `${name} (Dingin)`;
            }

            if (existingItem) {
                // Jika sudah ada, tambahkan quantity
                existingItem.quantity += quantity;
                // Update URL gambar jika belum ada
                if (!existingItem.imageUrl && imageUrl) {
                    existingItem.imageUrl = imageUrl;
                }
            } else {
                // Jika belum ada, tambahkan item baru
                cart.push({
                    id: id,
                    name: displayName,
                    originalName: name,
                    price: price,
                    quantity: quantity,
                    imageUrl: imageUrl,
                    variantType: variantType
                });
            }

            // Simpan ke sessionStorage
            sessionStorage.setItem('cart', JSON.stringify(cart));

            // Trigger storage event untuk halaman lain
            if (typeof window.dispatchEvent === 'function') {
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'cart'
                }));
            }

            // Update tampilan keranjang
            updateCartBadge(cart);
            updateCartItems();
        }

        // Update badge keranjang
        function updateCartBadge(cartData = null) {
            const cartBadge = document.getElementById('cart-badge');
            const cartToggleBadge = document.getElementById('cartToggleBadge');

            // Gunakan data keranjang yang diberikan atau ambil dari sessionStorage
            let cart = cartData || [];
            if (!cartData && sessionStorage.getItem('cart')) {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart'));
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                    sessionStorage.removeItem('cart');
                    cart = [];
                }
            }

            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);

            if (cartBadge) {
                if (totalItems > 0) {
                    cartBadge.textContent = totalItems;
                    cartBadge.style.display = 'inline-block';

                    // Animasi badge
                    cartBadge.classList.add('pulse');
                    setTimeout(() => {
                        cartBadge.classList.remove('pulse');
                    }, 500);
                } else {
                    cartBadge.style.display = 'none';
                }
            }

            if (cartToggleBadge) {
                cartToggleBadge.textContent = totalItems;
            }

            // Enable/disable checkout button
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (checkoutBtn) {
                if (totalItems > 0) {
                    checkoutBtn.removeAttribute('disabled');
                    checkoutBtn.classList.add('active');
                } else {
                    checkoutBtn.setAttribute('disabled', 'disabled');
                    checkoutBtn.classList.remove('active');
                }
            }
        }

        // Update cart items sidebar
        function updateCartItems() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartSubtotal = document.getElementById('cartSubtotal');
            const cartTax = document.getElementById('cartTax');
            const cartTotal = document.getElementById('cartTotal');

            if (!cartItemsContainer || !cartSubtotal) return;

            let cart = [];
            if (sessionStorage.getItem('cart')) {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart'));
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                    sessionStorage.removeItem('cart');
                    cart = [];
                }
            }

            // Calculate subtotal
            const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);

            // Gunakan nilai pajak dari pengaturan
            const taxPercentage = {{ isset($paymentSettings['payment_tax_percentage']) ? $paymentSettings['payment_tax_percentage'] : 10 }} / 100;
            const tax = subtotal * taxPercentage;
            const total = subtotal + tax;

            cartSubtotal.textContent = `Rp ${formatNumber(subtotal)}`;
            cartTax.textContent = `Rp ${formatNumber(tax)}`;
            cartTotal.textContent = `Rp ${formatNumber(total)}`;

            // Clear container
            cartItemsContainer.innerHTML = '';

            // Show empty cart message if needed
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart-message">
                        <i class="fas fa-shopping-basket mb-3"></i>
                        <p>Keranjang Anda masih kosong</p>
                        <small class="text-muted">Tambahkan menu favorit Anda</small>
                    </div>
                `;
                return;
            }

            // Add items to cart
            cart.forEach(item => {
                // Gunakan URL gambar dari item keranjang jika tersedia
                let imgSrc = item.imageUrl || 'https://via.placeholder.com/50x50';

                // Jika tidak ada URL gambar, coba ambil dari elemen produk
                if (!imgSrc || imgSrc === 'https://via.placeholder.com/50x50') {
                    const productItem = Array.from(productItems).find(product => {
                        return parseInt(product.querySelector('.add-to-cart').dataset.id) === item.id;
                    });

                    if (productItem) {
                        imgSrc = productItem.querySelector('.card-img-top').src;
                        // Update item keranjang dengan URL gambar baru
                        item.imageUrl = imgSrc;
                        sessionStorage.setItem('cart', JSON.stringify(cart));
                    }
                }

                const cartItemElement = document.createElement('div');
                cartItemElement.className = 'cart-item';
                cartItemElement.innerHTML = `
                    <div class="cart-item-image">
                        <img src="${imgSrc}" alt="${item.name}">
                    </div>
                    <div class="cart-item-info">
                        <p class="cart-item-name">${item.name}</p>
                        <span class="cart-item-price">Rp ${formatNumber(item.price)}</span>
                        <div class="cart-item-controls">
                            <div class="cart-item-quantity">
                                <button class="decrease-cart-item" data-id="${item.id}" data-variant="${item.variantType || ''}">-</button>
                                <span>${item.quantity}</span>
                                <button class="increase-cart-item" data-id="${item.id}" data-variant="${item.variantType || ''}">+</button>
                            </div>
                            <button class="cart-item-remove" data-id="${item.id}" data-variant="${item.variantType || ''}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItemElement);
            });

            // Tambah event listeners untuk tombol di keranjang
            document.querySelectorAll('.increase-cart-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;
                    updateCartItemQuantity(itemId, 1, variantType);
                });
            });

            document.querySelectorAll('.decrease-cart-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;
                    updateCartItemQuantity(itemId, -1, variantType);
                });
            });

            document.querySelectorAll('.cart-item-remove').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = parseInt(this.dataset.id);
                    const variantType = this.dataset.variant || null;
                    removeCartItem(itemId, variantType);
                });
            });
        }

        // Update item quantity in cart
        function updateCartItemQuantity(itemId, change, variantType) {
            let cart = [];
            if (sessionStorage.getItem('cart')) {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart'));
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                    return;
                }
            }

            const itemIndex = cart.findIndex(item =>
                item.id === itemId && (variantType ? item.variantType === variantType : !item.variantType)
            );

            if (itemIndex !== -1) {
                cart[itemIndex].quantity += change;

                if (cart[itemIndex].quantity <= 0) {
                    // Remove item if quantity is 0 or less
                    cart.splice(itemIndex, 1);
                }

                sessionStorage.setItem('cart', JSON.stringify(cart));
                updateCartBadge(cart);
                updateCartItems();
            }
        }

        // Remove item from cart
        function removeCartItem(itemId, variantType) {
            let cart = [];
            if (sessionStorage.getItem('cart')) {
                try {
                    cart = JSON.parse(sessionStorage.getItem('cart'));
                } catch (e) {
                    console.error('Error parsing cart data from sessionStorage', e);
                    return;
                }
            }

            cart = cart.filter(item =>
                !(item.id === itemId && (variantType ? item.variantType === variantType : !item.variantType))
            );

            sessionStorage.setItem('cart', JSON.stringify(cart));
            updateCartBadge(cart);
            updateCartItems();
        }

        // Mobile cart toggle functionality
        const showCartBtn = document.getElementById('showCartBtn');
        const closeCartBtn = document.getElementById('closeCart');
        const cartSidebar = document.getElementById('cartSidebar');

        if (showCartBtn && closeCartBtn && cartSidebar) {
            showCartBtn.addEventListener('click', function() {
                cartSidebar.classList.add('show');
            });

            closeCartBtn.addEventListener('click', function() {
                cartSidebar.classList.remove('show');
            });
        }

        // Inisialisasi keranjang saat halaman dimuat
        updateCartBadge();
        updateCartItems();

        // Listen for storage events
        window.addEventListener('storage', function(e) {
            if (e.key === 'cart') {
                updateCartBadge();
                updateCartItems();
            }
        });

        // Format angka ke format rupiah
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Tampilkan notifikasi
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
                    <span class="notification-text">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 1500); // 1.5 detik saja untuk menampilkan notifikasi
        }
});
</script>
@endsection
