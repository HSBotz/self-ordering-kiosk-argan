@extends('admin.layouts.app')

@section('title', 'Pengaturan Pembayaran')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fas fa-money-bill-wave me-2"></i>Pengaturan Pembayaran</span>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.settings.payment.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Metode Pembayaran -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="payment_cash_enabled" name="payment_cash_enabled" value="1" {{ isset($settings['payment_cash_enabled']) && $settings['payment_cash_enabled'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cash_enabled">
                                    <i class="fas fa-money-bill text-success me-2"></i>Pembayaran Tunai
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="payment_qris_enabled" name="payment_qris_enabled" value="1" {{ isset($settings['payment_qris_enabled']) && $settings['payment_qris_enabled'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_qris_enabled">
                                    <i class="fas fa-qrcode text-primary me-2"></i>Pembayaran QRIS
                                </label>
                                <div class="mt-3 ms-4 {{ isset($settings['payment_qris_enabled']) && $settings['payment_qris_enabled'] == '1' ? '' : 'd-none' }}" id="qris-settings">
                                    <div class="mb-3">
                                        <label for="payment_qris_image" class="form-label">Gambar QRIS</label>
                                        <input class="form-control @error('payment_qris_image') is-invalid @enderror" type="file" id="payment_qris_image" name="payment_qris_image">
                                        <div class="form-text">Format: JPG, PNG. Maks: 200MB.</div>
                                        @error('payment_qris_image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if(isset($settings['payment_qris_image']) && !empty($settings['payment_qris_image']))
                                            <div class="mt-2">
                                                <img src="{{ asset($settings['payment_qris_image']) }}" alt="QRIS" class="img-thumbnail" style="max-height: 200px;">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="remove_qris_image" name="remove_qris_image" value="1">
                                                    <label class="form-check-label" for="remove_qris_image">
                                                        Hapus gambar QRIS
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="payment_debit_enabled" name="payment_debit_enabled" value="1" {{ isset($settings['payment_debit_enabled']) && $settings['payment_debit_enabled'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_debit_enabled">
                                    <i class="fas fa-id-card text-info me-2"></i>Pembayaran Kartu Debit/Kredit
                                </label>
                                <div class="mt-3 ms-4 {{ isset($settings['payment_debit_enabled']) && $settings['payment_debit_enabled'] == '1' ? '' : 'd-none' }}" id="debit-settings">
                                    <div class="mb-3">
                                        <label for="payment_debit_cards" class="form-label">Kartu yang Didukung</label>
                                        <input type="text" class="form-control" id="payment_debit_cards" name="payment_debit_cards" value="{{ $settings['payment_debit_cards'] ?? 'Visa, Mastercard, JCB, American Express' }}" placeholder="Contoh: Visa, Mastercard, JCB">
                                        <div class="form-text">Pisahkan dengan koma jika lebih dari satu</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Harga dan Pajak -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>Pengaturan Harga & Pajak</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="payment_tax_percentage" class="form-label">Persentase Pajak (%)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('payment_tax_percentage') is-invalid @enderror" id="payment_tax_percentage" name="payment_tax_percentage" value="{{ $settings['payment_tax_percentage'] ?? '10' }}" min="0" max="100" step="0.1">
                                    <span class="input-group-text">%</span>
                                    @error('payment_tax_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Pajak akan ditambahkan ke total belanja</div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_currency" class="form-label">Mata Uang</label>
                                <input type="text" class="form-control @error('payment_currency') is-invalid @enderror" id="payment_currency" name="payment_currency" value="{{ $settings['payment_currency'] ?? 'Rp' }}">
                                @error('payment_currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_currency_position" class="form-label">Posisi Simbol Mata Uang</label>
                                <select class="form-select @error('payment_currency_position') is-invalid @enderror" id="payment_currency_position" name="payment_currency_position">
                                    <option value="before" {{ (isset($settings['payment_currency_position']) && $settings['payment_currency_position'] == 'before') ? 'selected' : '' }}>Sebelum angka (Rp 10.000)</option>
                                    <option value="after" {{ (isset($settings['payment_currency_position']) && $settings['payment_currency_position'] == 'after') ? 'selected' : '' }}>Setelah angka (10.000 Rp)</option>
                                </select>
                                @error('payment_currency_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_decimal_separator" class="form-label">Pemisah Desimal</label>
                                <select class="form-select @error('payment_decimal_separator') is-invalid @enderror" id="payment_decimal_separator" name="payment_decimal_separator">
                                    <option value="." {{ (isset($settings['payment_decimal_separator']) && $settings['payment_decimal_separator'] == '.') ? 'selected' : '' }}>Titik (.)</option>
                                    <option value="," {{ (isset($settings['payment_decimal_separator']) && $settings['payment_decimal_separator'] == ',') ? 'selected' : '' }}>Koma (,)</option>
                                </select>
                                @error('payment_decimal_separator')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_thousand_separator" class="form-label">Pemisah Ribuan</label>
                                <select class="form-select @error('payment_thousand_separator') is-invalid @enderror" id="payment_thousand_separator" name="payment_thousand_separator">
                                    <option value="." {{ (isset($settings['payment_thousand_separator']) && $settings['payment_thousand_separator'] == '.') ? 'selected' : '' }}>Titik (.)</option>
                                    <option value="," {{ (isset($settings['payment_thousand_separator']) && $settings['payment_thousand_separator'] == ',') ? 'selected' : '' }}>Koma (,)</option>
                                    <option value="space" {{ (isset($settings['payment_thousand_separator']) && $settings['payment_thousand_separator'] == 'space') ? 'selected' : '' }}>Spasi</option>
                                    <option value="none" {{ (isset($settings['payment_thousand_separator']) && $settings['payment_thousand_separator'] == 'none') ? 'selected' : '' }}>Tidak ada</option>
                                </select>
                                @error('payment_thousand_separator')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle QRIS settings visibility
    const qrisEnabled = document.getElementById('payment_qris_enabled');
    const qrisSettings = document.getElementById('qris-settings');

    qrisEnabled.addEventListener('change', function() {
        if (this.checked) {
            qrisSettings.classList.remove('d-none');
        } else {
            qrisSettings.classList.add('d-none');
        }
    });

    // Toggle Debit card settings visibility
    const debitEnabled = document.getElementById('payment_debit_enabled');
    const debitSettings = document.getElementById('debit-settings');

    debitEnabled.addEventListener('change', function() {
        if (this.checked) {
            debitSettings.classList.remove('d-none');
        } else {
            debitSettings.classList.add('d-none');
        }
    });
});
</script>
@endsection
