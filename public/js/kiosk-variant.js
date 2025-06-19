/**
 * Kiosk Variant Handler
 * Menangani pemilihan varian hot/ice untuk produk minuman
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi event listener untuk tombol varian
    initVariantButtons();

    // Inisialisasi item di keranjang
    updateVariantOptionsInCart();
});

/**
 * Menginisialisasi tombol-tombol varian (hot/ice)
 */
function initVariantButtons() {
    // Delegasi event untuk tombol varian pada modal produk
    document.addEventListener('click', function(e) {
        // Handler untuk button hot
        if (e.target.classList.contains('btn-variant-hot') || e.target.closest('.btn-variant-hot')) {
            const button = e.target.classList.contains('btn-variant-hot') ? e.target : e.target.closest('.btn-variant-hot');
            const productId = button.dataset.productId;
            const container = button.closest('.variant-selection');

            // Tandai tombol hot sebagai aktif
            setActiveVariant(container, 'hot');

            console.log('Varian HOT dipilih untuk produk ID:', productId);
        }

        // Handler untuk button ice
        if (e.target.classList.contains('btn-variant-ice') || e.target.closest('.btn-variant-ice')) {
            const button = e.target.classList.contains('btn-variant-ice') ? e.target : e.target.closest('.btn-variant-ice');
            const productId = button.dataset.productId;
            const container = button.closest('.variant-selection');

            // Tandai tombol ice sebagai aktif
            setActiveVariant(container, 'ice');

            console.log('Varian ICE dipilih untuk produk ID:', productId);
        }
    });
}

/**
 * Set tombol varian tertentu sebagai aktif
 */
function setActiveVariant(container, variant) {
    if (!container) return;

    // Hapus kelas active dari semua tombol
    container.querySelectorAll('.btn-variant').forEach(btn => {
        btn.classList.remove('active');
    });

    // Tambahkan kelas active pada tombol yang dipilih
    const targetButton = container.querySelector(`.btn-variant-${variant}`);
    if (targetButton) {
        targetButton.classList.add('active');
    }

    // Update nilai hidden input untuk variantType
    const variantInput = container.querySelector('input[name="variantType"]');
    if (variantInput) {
        variantInput.value = variant;
        console.log('Nilai variantType diupdate menjadi:', variant);
    }
}

/**
 * Update tampilan varian pada item di keranjang
 */
function updateVariantOptionsInCart() {
    // Cari semua item di keranjang yang memiliki opsi varian
    const cartItems = document.querySelectorAll('.cart-item[data-has-variants="true"]');

    cartItems.forEach(item => {
        const productId = item.dataset.productId;
        const variantType = item.dataset.variantType || '';
        const variantInfo = item.querySelector('.cart-item-variant');

        if (variantInfo) {
            if (variantType === 'hot') {
                variantInfo.innerHTML = '<span class="badge bg-danger"><i class="fas fa-fire"></i> Panas</span>';
            } else if (variantType === 'ice') {
                variantInfo.innerHTML = '<span class="badge bg-info"><i class="fas fa-snowflake"></i> Dingin</span>';
            } else {
                variantInfo.innerHTML = '<span class="badge bg-secondary">Pilih Varian</span>';
            }
        }
    });
}

/**
 * Simpan varian produk ke keranjang
 * @param {number} productId - ID produk
 * @param {string} variant - 'hot' atau 'ice'
 */
function saveVariantToCart(productId, variant) {
    // Dapatkan data keranjang dari localStorage
    let cart = JSON.parse(localStorage.getItem('kioskCart')) || [];

    // Cari item di keranjang
    const itemIndex = cart.findIndex(item => item.id === productId);
    if (itemIndex !== -1) {
        // Update varian
        cart[itemIndex].variantType = variant;
        console.log(`Varian ${variant} disimpan untuk produk ID ${productId}`);

        // Simpan kembali ke localStorage
        localStorage.setItem('kioskCart', JSON.stringify(cart));

        // Update tampilan keranjang
        updateVariantOptionsInCart();
    }
}

/**
 * Validasi varian sebelum checkout
 * @returns {boolean} - true jika semua produk dengan varian memiliki nilai varian yang valid
 */
function validateVariantsBeforeCheckout() {
    // Dapatkan data keranjang
    let cart = JSON.parse(localStorage.getItem('kioskCart')) || [];

    // Filter produk yang memerlukan varian tapi belum dipilih
    const productsWithoutVariant = cart.filter(item =>
        item.hasVariants === true &&
        (!item.variantType || (item.variantType !== 'hot' && item.variantType !== 'ice'))
    );

    if (productsWithoutVariant.length > 0) {
        alert('Ada minuman yang belum dipilih varian (panas/dingin). Silakan pilih varian terlebih dahulu.');
        console.error('Produk tanpa varian:', productsWithoutVariant);
        return false;
    }

    return true;
}
