@extends('layouts.app')

@section('styles')
<style>
    .order-type-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem 0;
    }

    .order-type-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .order-type-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.5px;
    }

    .order-type-header p {
        font-size: 1.1rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }

    .order-types {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2rem;
        width: 100%;
        max-width: 900px;
    }

    .order-type-card {
        flex: 1;
        min-width: 280px;
        max-width: 380px;
        height: 320px;
        background-color: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.8rem;
        text-align: center;
        border: 2px solid transparent;
    }

    .order-type-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(106, 52, 18, 0.15);
        border-color: var(--accent-color);
    }

    .order-type-card:active {
        transform: scale(0.98);
    }

    .order-type-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: white;
        font-size: 2.8rem;
        box-shadow: 0 10px 20px rgba(106, 52, 18, 0.2);
        transition: all 0.3s ease;
    }

    .order-type-card:hover .order-type-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 15px 30px rgba(106, 52, 18, 0.3);
    }

    .order-type-body {
        width: 100%;
    }

    .order-type-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.7rem;
        color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .order-type-card:hover .order-type-title {
        transform: scale(1.05);
    }

    .order-type-desc {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .order-type-btn {
        width: 100%;
        padding: 0.8rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        box-shadow: 0 5px 15px rgba(106, 52, 18, 0.15);
    }

    .order-type-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(106, 52, 18, 0.25);
    }

    .order-type-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #ffb74d, #ff9800);
        color: white;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        font-size: 0.8rem;
        z-index: 2;
    }

    .order-type-card.popular {
        border: 2px solid var(--accent-color);
    }

    .order-type-card.popular::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 183, 77, 0.05), rgba(255, 152, 0, 0.05));
        z-index: 1;
    }

    /* Animation */
    .fade-in-up {
        animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 10px 20px rgba(106, 52, 18, 0.2);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(106, 52, 18, 0.3);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 10px 20px rgba(106, 52, 18, 0.2);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .order-type-header h1 {
            font-size: 2.2rem;
        }

        .order-type-card {
            flex: none;
            width: 100%;
            height: 300px;
            padding: 1.5rem;
        }

        .order-type-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
            margin-bottom: 1.2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="order-type-container">
    <div class="order-type-header" data-aos="fade-down">
        <h1>Pilih Tipe Pesanan</h1>
        <p>Silakan pilih apakah Anda ingin makan di tempat atau bawa pulang pesanan Anda</p>
    </div>

    <div class="order-types">
        <div class="order-type-card popular" data-aos="fade-up" data-aos-delay="100" onclick="selectOrderType('dine-in')">
            <div class="order-type-badge">Paling Populer</div>
            <div class="order-type-icon pulse">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="order-type-body">
                <h3 class="order-type-title">Dine In</h3>
                <p class="order-type-desc">Nikmati hidangan di tempat dengan suasana kedai yang nyaman dan pelayanan terbaik</p>
                <form action="{{ route('kiosk.process-order-type') }}" method="POST" id="dine-in-form">
                    @csrf
                    <input type="hidden" name="order_type" value="dine-in">
                    <button type="submit" class="btn btn-primary order-type-btn">
                        <i class="fas fa-check-circle me-2"></i> Pilih Dine In
                    </button>
                </form>
            </div>
        </div>

        <div class="order-type-card" data-aos="fade-up" data-aos-delay="200" onclick="selectOrderType('take-away')">
            <div class="order-type-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="order-type-body">
                <h3 class="order-type-title">Take Away</h3>
                <p class="order-type-desc">Bawa pulang pesanan Anda dan nikmati di mana saja dengan kemasan ramah lingkungan</p>
                <form action="{{ route('kiosk.process-order-type') }}" method="POST" id="take-away-form">
                    @csrf
                    <input type="hidden" name="order_type" value="take-away">
                    <button type="submit" class="btn btn-primary order-type-btn">
                        <i class="fas fa-check-circle me-2"></i> Pilih Take Away
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectOrderType(type) {
        // Simpan tipe pesanan di session storage (untuk frontend)
        sessionStorage.setItem('orderType', type);

        // Animasi sederhana sebelum submit form
        const cards = document.querySelectorAll('.order-type-card');
        cards.forEach(card => {
            if (card.querySelector('.order-type-title').textContent.toLowerCase().includes(type)) {
                card.style.transform = 'scale(1.05)';
                card.style.boxShadow = '0 20px 40px rgba(106, 52, 18, 0.3)';
                card.style.borderColor = 'var(--accent-color)';
                card.querySelector('.order-type-icon').style.transform = 'scale(1.1) rotate(10deg)';
            } else {
                card.style.opacity = '0.5';
                card.style.transform = 'scale(0.95)';
            }
        });

        // Submit form setelah delay singkat
        setTimeout(() => {
            if (type === 'dine-in') {
                document.getElementById('dine-in-form').submit();
            } else {
                document.getElementById('take-away-form').submit();
            }
        }, 500);
    }

    // Cek apakah sudah memilih tipe pesanan sebelumnya
    document.addEventListener('DOMContentLoaded', function() {
        // Reset tipe pesanan setiap kali halaman ini dibuka
        sessionStorage.removeItem('orderType');
    });
</script>
@endsection
