@extends('admin.layouts.app')

@section('title', 'Daftar Pesanan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Tambahkan elemen audio untuk notifikasi -->
<audio id="notification-sound" preload="auto" style="display: none;">
    <source src="{{ asset('audio/notification.mp3') }}" type="audio/mpeg">
</audio>

<!-- Form untuk bulk delete - ditempatkan di luar tabel -->
<form id="bulkDeleteForm" action="{{ url('admin/orders/bulk-destroy') }}" method="POST">
    @csrf
    @method('DELETE')
    <div id="bulk-delete-order-ids-container">
        <!-- Checkbox values akan ditambahkan oleh JavaScript -->
    </div>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Pesanan</span>
        <div>
            <a href="{{ route('admin.orders.fix-variants') }}" class="btn btn-sm btn-warning me-2" onclick="return confirm('Yakin ingin memperbaiki semua varian yang NULL?')">
                <i class="fas fa-magic me-1"></i> Perbaiki Semua Varian
            </a>
            <div class="form-check form-switch d-inline-block me-3">
                <input class="form-check-input" type="checkbox" id="autoRefreshToggle" checked>
                <label class="form-check-label" for="autoRefreshToggle">Auto refresh</label>
            </div>
            <span id="refreshStatus" class="badge bg-success me-2">
                <i class="fas fa-sync-alt fa-spin"></i>
                <span id="refreshTimeLeft">5</span>s
            </span>
            <button id="manualRefresh" class="btn btn-sm btn-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(count($orders) > 0)
        <div class="mb-3">
            <button type="button" id="deleteSelected" class="btn btn-danger btn-sm disabled">
                <i class="fas fa-trash me-1"></i> Hapus Pesanan Terpilih
            </button>
            <button type="button" id="selectAll" class="btn btn-outline-secondary btn-sm ms-1">
                <i class="fas fa-check-square me-1"></i> Pilih Semua
            </button>
            <button type="button" id="deselectAll" class="btn btn-outline-secondary btn-sm ms-1 d-none">
                <i class="fas fa-square me-1"></i> Batal Pilih
            </button>
            <span id="selectedCount" class="badge bg-primary ms-2 d-none">0 item terpilih</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th width="30" class="text-center">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th class="text-center">Status</th>
                        <th>Pembayaran</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Varian</th>
                        <th>Tanggal</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @foreach($orders as $order)
                    <tr data-order-id="{{ $order->id }}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td class="order-number small">{{ $order->order_number }}</td>
                        <td class="customer-name small">{{ $order->customer_name ?? 'Anonim' }}</td>
                        <td class="total-amount small">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="order-status text-center">
                            @if($order->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($order->status == 'processing')
                                <span class="badge bg-info">Proses</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger">Batal</span>
                            @endif
                        </td>
                        <td class="order-payment small">{{ ucfirst($order->payment_method ?? 'Tunai') }}</td>
                        <td class="order-type text-center">
                            @if($order->order_type == 'dine-in')
                                <span class="badge bg-info"><i class="fas fa-utensils"></i></span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-shopping-bag"></i></span>
                            @endif
                        </td>
                        <td class="order-variants text-center">
                            @php
                                // Mengambil data varian secara manual dari database dengan array filtering
                                $hotItems = 0;
                                $iceItems = 0;
                                $noVariantItems = 0;
                                $nullVariantItems = 0;
                                $totalItems = count($order->orderItems);

                                foreach ($order->orderItems as $item) {
                                    if ($item->variant_type == 'hot') {
                                        $hotItems++;
                                    } elseif ($item->variant_type == 'ice') {
                                        $iceItems++;
                                    } elseif ($item->variant_type === null) {
                                        // Cek apakah produk dari kategori tanpa varian
                                        if ($item->product && $item->product->category && !$item->product->category->has_variants) {
                                            $noVariantItems++; // Produk dari kategori tanpa varian
                                        } else {
                                            $nullVariantItems++; // Produk dengan varian tapi belum dipilih
                                        }
                                    }
                                }

                                // Hitung item dengan varian yang valid
                                $itemsWithVariant = $hotItems + $iceItems + $noVariantItems;
                                $needsVariant = $totalItems - $noVariantItems; // Total item yang memerlukan varian
                                $hasVariant = $hotItems + $iceItems; // Item yang sudah memiliki varian
                            @endphp

                            @if($totalItems > 0)
                                @if($needsVariant == 0 || ($needsVariant > 0 && $hasVariant == $needsVariant))
                                    {{-- Semua item sudah memiliki varian yang valid --}}
                                    <span class="badge bg-success">{{ $itemsWithVariant }}/{{ $totalItems }}</span>
                                    <div class="mt-1 d-flex gap-1 justify-content-center">
                                        @if($hotItems > 0)<span class="badge bg-danger"><i class="fas fa-fire"></i> {{ $hotItems }}</span>@endif
                                        @if($iceItems > 0)<span class="badge bg-info"><i class="fas fa-snowflake"></i> {{ $iceItems }}</span>@endif
                                        @if($noVariantItems > 0)<span class="badge bg-secondary"><i class="fas fa-minus"></i> {{ $noVariantItems }}</span>@endif
                                    </div>
                                @elseif($hasVariant > 0)
                                    {{-- Sebagian item memiliki varian --}}
                                    <span class="badge bg-warning">{{ $hasVariant }}/{{ $needsVariant }}</span>
                                    <div class="mt-1 d-flex gap-1 justify-content-center">
                                        @if($hotItems > 0)<span class="badge bg-danger"><i class="fas fa-fire"></i> {{ $hotItems }}</span>@endif
                                        @if($iceItems > 0)<span class="badge bg-info"><i class="fas fa-snowflake"></i> {{ $iceItems }}</span>@endif
                                        @if($noVariantItems > 0)<span class="badge bg-secondary"><i class="fas fa-minus"></i> {{ $noVariantItems }}</span>@endif
                                    </div>
                                @else
                                    {{-- Tidak ada item yang memiliki varian --}}
                                    <span class="badge bg-danger">0/{{ $needsVariant }}</span>
                                    <div class="mt-1">
                                        <a href="{{ route('admin.orders.fix-variants', $order->id) }}" class="badge bg-warning text-dark">
                                            <i class="fas fa-magic"></i>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td class="order-date small">{{ $order->created_at->format('d/m/y H:i') }}</td>
                        <td class="order-actions text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm" title="Edit Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
        @else
        <div class="alert alert-info">
            Belum ada pesanan. Pesanan baru akan muncul di sini.
        </div>
        @endif
    </div>
</div>

<!-- Template untuk baris pesanan baru -->
<template id="orderRowTemplate">
    <tr data-order-id="">
        <td class="text-center">
            <input type="checkbox" class="form-check-input order-checkbox" name="order_ids[]">
        </td>
        <td class="order-number small"></td>
        <td class="customer-name small"></td>
        <td class="total-amount small"></td>
        <td class="order-status text-center"></td>
        <td class="order-payment small"></td>
        <td class="order-type text-center"></td>
        <td class="order-variants text-center"></td>
        <td class="order-date small"></td>
        <td class="order-actions text-center">
            <div class="btn-group btn-group-sm"></div>
        </td>
    </tr>
</template>

<!-- Template untuk item pesanan dengan varian -->
<template id="orderItemTemplate">
    <div class="order-item mb-1">
        <span class="item-name"></span>
        <span class="item-quantity"></span>
        <span class="variant-badge"></span>
    </div>
</template>

<!-- Modal konfirmasi hapus massal -->
<div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-labelledby="deleteSelectedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteSelectedModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan menghapus <span id="deleteCount">0</span> pesanan. Tindakan ini tidak dapat dibatalkan.</p>
                <p>Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">Ya, Hapus Semua</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="newOrderToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto">Pesanan Baru</strong>
            <small>Baru saja</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <span id="newOrderMessage"></span>
            <div id="orderItemsPreview" class="mt-2 small">
                <!-- Item pesanan akan ditambahkan di sini -->
            </div>
        </div>
    </div>
</div>

<style>
    .variant-badge {
        font-size: 0.6rem;
        padding: 0.12rem 0.3rem;
        border-radius: 3px;
        margin-left: 0.3rem;
        display: inline-block;
        border: 1px solid;
        margin-bottom: 3px;
    }
    .variant-hot {
        background-color: #ffcccb;
        color: #d63031;
        border-color: #d63031;
    }
    .variant-ice {
        background-color: #c7ecee;
        color: #0984e3;
        border-color: #0984e3;
    }
    .item-quantity {
        color: #666;
        font-size: 0.75rem;
        margin-left: 0.2rem;
    }

    /* Gaya untuk konsistensi tabel */
    .table th {
        white-space: nowrap;
        vertical-align: middle;
        font-size: 0.82rem;
        padding: 0.4rem 0.5rem;
    }
    .table td {
        vertical-align: middle;
        padding: 0.3rem 0.5rem;
        font-size: 0.825rem;
    }
    .table .small {
        font-size: 0.78rem;
    }
    .table .btn-sm {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
    }
    .table form {
        margin-bottom: 0;
    }
    .table .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    .table .btn-group-sm > .btn {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
    }
    .highlight-new {
        animation: highlightFade 3s;
    }
    @keyframes highlightFade {
        0% { background-color: rgba(76, 175, 80, 0.3); }
        100% { background-color: transparent; }
    }
    .selected-row {
        background-color: #f8f9fa;
    }
    .card-header {
        padding: 0.5rem 1rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    /* Memperbaiki tampilan pagination */
    .pagination {
        margin-bottom: 0;
    }
    .page-item .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
</style>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk mengubah huruf pertama menjadi kapital
        function ucfirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // Variabel untuk polling
        let autoRefreshEnabled = true;
        let refreshInterval = 5; // diubah dari 30 detik menjadi 5 detik
        let currentTimer = refreshInterval;
        let timerInterval;
        let lastOrderId = {{ $orders->count() > 0 ? $orders->first()->id : 0 }};
        let timezone = "{{ \App\Models\SiteSetting::getValue('store_timezone', 'WIB') }}";

        // Elemen DOM
        const autoRefreshToggle = document.getElementById('autoRefreshToggle');
        const refreshStatus = document.getElementById('refreshStatus');
        const refreshTimeLeft = document.getElementById('refreshTimeLeft');
        const manualRefresh = document.getElementById('manualRefresh');
        const ordersTableBody = document.getElementById('ordersTableBody');
        const noOrdersRow = document.getElementById('noOrdersRow');
        const orderRowTemplate = document.getElementById('orderRowTemplate');
        const orderItemTemplate = document.getElementById('orderItemTemplate');
        const newOrderToast = document.getElementById('newOrderToast');
        const newOrderMessage = document.getElementById('newOrderMessage');
        const orderItemsPreview = document.getElementById('orderItemsPreview');

        // Elemen untuk fitur select/delete
        const checkAllBox = document.getElementById('checkAll');
        const deleteSelectedBtn = document.getElementById('deleteSelected');
        const selectedCountBadge = document.getElementById('selectedCount');
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        const deleteSelectedModal = new bootstrap.Modal(document.getElementById('deleteSelectedModal'));
        const deleteCountSpan = document.getElementById('deleteCount');
        const confirmBulkDeleteBtn = document.getElementById('confirmBulkDelete');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        // Tampilkan waktu refresh awal yang benar
        refreshTimeLeft.textContent = refreshInterval;

        // Inisialisasi toast
        const toast = new bootstrap.Toast(newOrderToast);

        // Fungsi untuk memperbarui jumlah item terpilih
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.order-checkbox:checked');
            const count = checkboxes.length;

            selectedCountBadge.textContent = count + ' item terpilih';
            deleteCountSpan.textContent = count;

            if (count > 0) {
                deleteSelectedBtn.classList.remove('disabled');
                selectedCountBadge.classList.remove('d-none');
            } else {
                deleteSelectedBtn.classList.add('disabled');
                selectedCountBadge.classList.add('d-none');
            }
        }

        // Event untuk checkbox "select all"
        if (checkAllBox) {
            checkAllBox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.order-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    if (this.checked) {
                        checkbox.closest('tr').classList.add('selected-row');
                    } else {
                        checkbox.closest('tr').classList.remove('selected-row');
                    }
                });
                updateSelectedCount();

                if (this.checked) {
                    selectAllBtn.classList.add('d-none');
                    deselectAllBtn.classList.remove('d-none');
                } else {
                    selectAllBtn.classList.remove('d-none');
                    deselectAllBtn.classList.add('d-none');
                }
            });
        }

        // Event untuk "Pilih Semua" button
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                checkAllBox.checked = true;

                const checkboxes = document.querySelectorAll('.order-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.closest('tr').classList.add('selected-row');
                });

                selectAllBtn.classList.add('d-none');
                deselectAllBtn.classList.remove('d-none');
                updateSelectedCount();
            });
        }

        // Event untuk "Batal Pilih" button
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                checkAllBox.checked = false;

                const checkboxes = document.querySelectorAll('.order-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.closest('tr').classList.remove('selected-row');
                });

                selectAllBtn.classList.remove('d-none');
                deselectAllBtn.classList.add('d-none');
                updateSelectedCount();
            });
        }

        // Event listener untuk checkboxes individual
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('order-checkbox')) {
                if (e.target.checked) {
                    e.target.closest('tr').classList.add('selected-row');
                } else {
                    e.target.closest('tr').classList.remove('selected-row');
                    checkAllBox.checked = false;
                }
                updateSelectedCount();
            }
        });

        // Event untuk tombol hapus terpilih
        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', function() {
                const selectedCount = document.querySelectorAll('.order-checkbox:checked').length;
                if (selectedCount > 0) {
                    deleteCountSpan.textContent = selectedCount;
                    deleteSelectedModal.show();
                }
            });
        }

        // Event untuk konfirmasi hapus massal
        if (confirmBulkDeleteBtn) {
            confirmBulkDeleteBtn.addEventListener('click', function() {
                try {
                    // Dapatkan semua checkbox yang dicentang
                    const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
                    console.log('Selected checkboxes:', selectedCheckboxes.length);

                    if (selectedCheckboxes.length === 0) {
                        alert('Tidak ada pesanan yang dipilih');
                        return;
                    }

                    // Hapus semua hidden input sebelumnya
                    const container = document.getElementById('bulk-delete-order-ids-container');
                    container.innerHTML = '';

                    // Buat hidden input untuk setiap checkbox yang dicentang
                    selectedCheckboxes.forEach(checkbox => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'order_ids[]';
                        input.value = checkbox.value;
                        container.appendChild(input);
                        console.log('Added input for order ID:', checkbox.value);
                    });

                    console.log('Form action:', bulkDeleteForm.action);
                    console.log('Form method:', bulkDeleteForm.method);
                    console.log('Form elements:', bulkDeleteForm.elements.length);
                    console.log('Selected IDs:', Array.from(selectedCheckboxes).map(cb => cb.value));

                    // Tutup modal dan submit form
                    deleteSelectedModal.hide();

                    // Submit form setelah delay kecil untuk memastikan modal benar-benar tertutup
                    setTimeout(() => {
                        bulkDeleteForm.submit();
                    }, 100);
                } catch (error) {
                    console.error('Error in bulk delete action:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                }
            });
        }

        // Toggle auto refresh
        autoRefreshToggle.addEventListener('change', function() {
            autoRefreshEnabled = this.checked;
            if (autoRefreshEnabled) {
                startTimer();
                refreshStatus.classList.remove('bg-secondary');
                refreshStatus.classList.add('bg-success');
            } else {
                stopTimer();
                refreshStatus.classList.remove('bg-success');
                refreshStatus.classList.add('bg-secondary');
            }
        });

        // Manual refresh
        manualRefresh.addEventListener('click', function() {
            fetchOrders();
            resetTimer();
        });

        // Fungsi untuk memulai timer
        function startTimer() {
            stopTimer();
            timerInterval = setInterval(() => {
                currentTimer--;
                refreshTimeLeft.textContent = currentTimer;

                if (currentTimer <= 0) {
                    fetchOrders();
                    resetTimer();
                }
            }, 1000);
        }

        // Fungsi untuk menghentikan timer
        function stopTimer() {
            clearInterval(timerInterval);
        }

        // Fungsi untuk mengatur ulang timer
        function resetTimer() {
            currentTimer = refreshInterval;
            refreshTimeLeft.textContent = currentTimer;
        }

        // Fungsi untuk mengambil data pesanan terbaru
        function fetchOrders() {
            fetch('/admin/orders/api/latest')
                .then(response => response.json())
                .then(data => {
                    updateOrdersTable(data.orders);
                    checkNewOrders(data.orders);
                    // Update timezone jika berubah dari server
                    if (data.timezone && data.timezone !== timezone) {
                        document.querySelectorAll('.order-date').forEach(dateElement => {
                            const dateText = dateElement.textContent;
                            // Ganti zona waktu lama dengan yang baru
                            dateElement.textContent = dateText.replace(timezone, data.timezone);
                        });
                        // Update variabel timezone
                        timezone = data.timezone;
                    }
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                });
        }

        // Fungsi untuk memperbarui tabel pesanan
        function updateOrdersTable(orders) {
            if (orders.length === 0) {
                if (!document.getElementById('noOrdersRow')) {
                    const emptyRow = document.createElement('tr');
                    emptyRow.id = 'noOrdersRow';
                    emptyRow.innerHTML = `
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-muted"></i>
                            <p>Belum ada pesanan</p>
                        </td>
                    `;
                    ordersTableBody.appendChild(emptyRow);
                }
                return;
            }

            // Hapus pesan "tidak ada pesanan" jika ada
            const noOrdersRow = document.getElementById('noOrdersRow');
            if (noOrdersRow) {
                noOrdersRow.remove();
            }

            // Dapatkan ID pesanan yang ada sebelum update dan status checkbox mereka
            const checkboxState = {};
            document.querySelectorAll('#ordersTableBody tr[data-order-id]').forEach(row => {
                const orderId = row.dataset.orderId;
                const checkbox = row.querySelector('.order-checkbox');
                if (checkbox) {
                    checkboxState[orderId] = checkbox.checked;
                }
            });

            // Dapatkan ID pesanan yang ada sebelum update
            const existingOrderIds = Array.from(
                document.querySelectorAll('#ordersTableBody tr[data-order-id]')
            ).map(row => parseInt(row.dataset.orderId));

            // Buat array untuk menyimpan baris yang sudah diperbarui
            const updatedRows = [];

            // Perbarui atau tambahkan pesanan baru
            orders.forEach(order => {
                const existingRow = document.querySelector(`tr[data-order-id="${order.id}"]`);

                if (existingRow) {
                    // Perbarui baris yang sudah ada
                    updateOrderRow(existingRow, order);

                    // Pertahankan status checkbox
                    if (checkboxState[order.id]) {
                        const checkbox = existingRow.querySelector('.order-checkbox');
                        if (checkbox) {
                            checkbox.checked = true;
                            existingRow.classList.add('selected-row');
                        }
                    }

                    updatedRows.push(existingRow);
                } else {
                    // Tambahkan baris baru
                    const newRow = createOrderRow(order);
                    updatedRows.push(newRow);
                }
            });

            // Urutkan baris berdasarkan ID pesanan (terbaru dulu)
            updatedRows.sort((a, b) => {
                return parseInt(b.dataset.orderId) - parseInt(a.dataset.orderId);
            });

            // Kosongkan tabel dan tambahkan kembali baris yang sudah diperbarui
            ordersTableBody.innerHTML = '';
            updatedRows.forEach(row => {
                // Tambahkan animasi highlight untuk baris baru
                if (!existingOrderIds.includes(parseInt(row.dataset.orderId))) {
                    row.classList.add('highlight-new');
                    setTimeout(() => {
                        row.classList.remove('highlight-new');
                    }, 3000);
                }
                ordersTableBody.appendChild(row);

                // Tambahkan event listener untuk checkbox
                const checkbox = row.querySelector('.order-checkbox');
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            this.closest('tr').classList.add('selected-row');
                        } else {
                            this.closest('tr').classList.remove('selected-row');
                            checkAllBox.checked = false;
                        }
                        updateSelectedCount();
                    });
                }
            });

            // Update jumlah item yang dipilih
            updateSelectedCount();
        }

        // Fungsi untuk memformat tanggal
        function formatDate(dateString) {
            const date = new Date(dateString);
            return `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${String(date.getFullYear()).slice(-2)} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
        }

        // Helper untuk memformat angka
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Fungsi untuk membuat baris pesanan baru
        function createOrderRow(order) {
            const template = orderRowTemplate.content.cloneNode(true);
            const row = template.querySelector('tr');

            row.dataset.orderId = order.id;

            // Set value untuk checkbox
            const checkbox = row.querySelector('.order-checkbox');
            checkbox.value = order.id;

            row.querySelector('.order-number').textContent = order.order_number;
            row.querySelector('.customer-name').textContent = order.customer_name || 'Anonim';
            row.querySelector('.total-amount').textContent = formatNumber(order.total_amount);

            // Status pesanan
            const statusCell = row.querySelector('.order-status');
            if (order.status === 'pending') {
                statusCell.innerHTML = '<span class="badge bg-warning">Pending</span>';
            } else if (order.status === 'processing') {
                statusCell.innerHTML = '<span class="badge bg-info">Proses</span>';
            } else if (order.status === 'completed') {
                statusCell.innerHTML = '<span class="badge bg-success">Selesai</span>';
            } else if (order.status === 'cancelled') {
                statusCell.innerHTML = '<span class="badge bg-danger">Batal</span>';
            }

            // Metode pembayaran
            const paymentCell = row.querySelector('.order-payment');
            paymentCell.textContent = ucfirst(order.payment_method || 'Tunai');

            // Tipe pesanan
            const typeCell = row.querySelector('.order-type');
            if (order.order_type === 'dine-in') {
                typeCell.innerHTML = '<span class="badge bg-info"><i class="fas fa-utensils"></i></span>';
            } else {
                typeCell.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-shopping-bag"></i></span>';
            }

            // Varian pesanan
            const variantsCell = row.querySelector('.order-variants');
            if (order.order_items && order.order_items.length > 0) {
                // Hitung jumlah item berdasarkan varian
                let hotCount = 0;
                let iceCount = 0;
                let noVariantCount = 0;
                let nullVariantCount = 0;
                let totalItems = order.order_items.length;

                order.order_items.forEach(item => {
                    if (item.variant_type === 'hot') {
                        hotCount++;
                    } else if (item.variant_type === 'ice') {
                        iceCount++;
                    } else if (item.variant_type === null) {
                        // Cek apakah produk dari kategori tanpa varian
                        if (item.product && item.product.category && item.product.category.has_variants === false) {
                            noVariantCount++; // Produk dari kategori tanpa varian
                        } else {
                            nullVariantCount++; // Produk dengan varian tapi belum dipilih
                        }
                    }
                });

                // Hitung item dengan varian yang valid
                let itemsWithVariant = hotCount + iceCount + noVariantCount;
                let needsVariant = totalItems - noVariantCount; // Total item yang memerlukan varian
                let hasVariant = hotCount + iceCount; // Item yang sudah memiliki varian

                // Buat struktur HTML untuk varian
                let variantHTML = '';

                if (needsVariant === 0 || (needsVariant > 0 && hasVariant === needsVariant)) {
                    // Semua item sudah memiliki varian yang valid
                    variantHTML = `
                        <span class="badge bg-success">${itemsWithVariant}/${totalItems}</span>
                        <div class="mt-1 d-flex gap-1 justify-content-center">
                            ${hotCount > 0 ? `<span class="badge bg-danger"><i class="fas fa-fire"></i> ${hotCount}</span>` : ''}
                            ${iceCount > 0 ? `<span class="badge bg-info"><i class="fas fa-snowflake"></i> ${iceCount}</span>` : ''}
                            ${noVariantCount > 0 ? `<span class="badge bg-secondary"><i class="fas fa-minus"></i> ${noVariantCount}</span>` : ''}
                        </div>
                    `;
                } else if (hasVariant > 0) {
                    // Sebagian item memiliki varian
                    variantHTML = `
                        <span class="badge bg-warning">${hasVariant}/${needsVariant}</span>
                        <div class="mt-1 d-flex gap-1 justify-content-center">
                            ${hotCount > 0 ? `<span class="badge bg-danger"><i class="fas fa-fire"></i> ${hotCount}</span>` : ''}
                            ${iceCount > 0 ? `<span class="badge bg-info"><i class="fas fa-snowflake"></i> ${iceCount}</span>` : ''}
                            ${noVariantCount > 0 ? `<span class="badge bg-secondary"><i class="fas fa-minus"></i> ${noVariantCount}</span>` : ''}
                        </div>
                    `;
                } else {
                    // Tidak ada item yang memiliki varian
                    variantHTML = `
                        <span class="badge bg-danger">0/${needsVariant}</span>
                        <div class="mt-1">
                            <a href="/admin/orders/fix-variants/${order.id}" class="badge bg-warning text-dark">
                                <i class="fas fa-magic"></i>
                            </a>
                        </div>
                    `;
                }

                variantsCell.innerHTML = variantHTML;
            } else {
                variantsCell.innerHTML = '-';
            }

            // Tanggal pesanan
            row.querySelector('.order-date').textContent = formatDate(order.created_at);

            // Tombol aksi
            const actionsContainer = row.querySelector('.order-actions .btn-group-sm');
            actionsContainer.innerHTML = `
                <a href="/admin/orders/${order.id}" class="btn btn-info btn-sm" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/admin/orders/${order.id}/edit" class="btn btn-warning btn-sm" title="Edit Status">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="/admin/orders/${order.id}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            `;

            return row;
        }

        // Fungsi untuk memperbarui baris pesanan yang sudah ada
        function updateOrderRow(row, order) {
            row.querySelector('.order-number').textContent = order.order_number;
            row.querySelector('.customer-name').textContent = order.customer_name || 'Anonim';
            row.querySelector('.total-amount').textContent = formatNumber(order.total_amount);

            // Status pesanan
            const statusCell = row.querySelector('.order-status');
            if (order.status === 'pending') {
                statusCell.innerHTML = '<span class="badge bg-warning">Pending</span>';
            } else if (order.status === 'processing') {
                statusCell.innerHTML = '<span class="badge bg-info">Proses</span>';
            } else if (order.status === 'completed') {
                statusCell.innerHTML = '<span class="badge bg-success">Selesai</span>';
            } else if (order.status === 'cancelled') {
                statusCell.innerHTML = '<span class="badge bg-danger">Batal</span>';
            }

            // Metode pembayaran
            const paymentCell = row.querySelector('.order-payment');
            paymentCell.textContent = ucfirst(order.payment_method || 'Tunai');

            // Tipe pesanan
            const typeCell = row.querySelector('.order-type');
            if (order.order_type === 'dine-in') {
                typeCell.innerHTML = '<span class="badge bg-info"><i class="fas fa-utensils"></i></span>';
            } else {
                typeCell.innerHTML = '<span class="badge bg-secondary"><i class="fas fa-shopping-bag"></i></span>';
            }

            // Varian pesanan
            const variantsCell = row.querySelector('.order-variants');
            if (order.order_items && order.order_items.length > 0) {
                // Hitung jumlah item berdasarkan varian
                let hotCount = 0;
                let iceCount = 0;
                let noVariantCount = 0;
                let nullVariantCount = 0;
                let totalItems = order.order_items.length;

                order.order_items.forEach(item => {
                    if (item.variant_type === 'hot') {
                        hotCount++;
                    } else if (item.variant_type === 'ice') {
                        iceCount++;
                    } else if (item.variant_type === null) {
                        // Cek apakah produk dari kategori tanpa varian
                        if (item.product && item.product.category && item.product.category.has_variants === false) {
                            noVariantCount++; // Produk dari kategori tanpa varian
                        } else {
                            nullVariantCount++; // Produk dengan varian tapi belum dipilih
                        }
                    }
                });

                // Hitung item dengan varian yang valid
                let itemsWithVariant = hotCount + iceCount + noVariantCount;
                let needsVariant = totalItems - noVariantCount; // Total item yang memerlukan varian
                let hasVariant = hotCount + iceCount; // Item yang sudah memiliki varian

                // Buat struktur HTML untuk varian
                let variantHTML = '';

                if (needsVariant === 0 || (needsVariant > 0 && hasVariant === needsVariant)) {
                    // Semua item sudah memiliki varian yang valid
                    variantHTML = `
                        <span class="badge bg-success">${itemsWithVariant}/${totalItems}</span>
                        <div class="mt-1 d-flex gap-1 justify-content-center">
                            ${hotCount > 0 ? `<span class="badge bg-danger"><i class="fas fa-fire"></i> ${hotCount}</span>` : ''}
                            ${iceCount > 0 ? `<span class="badge bg-info"><i class="fas fa-snowflake"></i> ${iceCount}</span>` : ''}
                            ${noVariantCount > 0 ? `<span class="badge bg-secondary"><i class="fas fa-minus"></i> ${noVariantCount}</span>` : ''}
                        </div>
                    `;
                } else if (hasVariant > 0) {
                    // Sebagian item memiliki varian
                    variantHTML = `
                        <span class="badge bg-warning">${hasVariant}/${needsVariant}</span>
                        <div class="mt-1 d-flex gap-1 justify-content-center">
                            ${hotCount > 0 ? `<span class="badge bg-danger"><i class="fas fa-fire"></i> ${hotCount}</span>` : ''}
                            ${iceCount > 0 ? `<span class="badge bg-info"><i class="fas fa-snowflake"></i> ${iceCount}</span>` : ''}
                            ${noVariantCount > 0 ? `<span class="badge bg-secondary"><i class="fas fa-minus"></i> ${noVariantCount}</span>` : ''}
                        </div>
                    `;
                } else {
                    // Tidak ada item yang memiliki varian
                    variantHTML = `
                        <span class="badge bg-danger">0/${needsVariant}</span>
                        <div class="mt-1">
                            <a href="/admin/orders/fix-variants/${order.id}" class="badge bg-warning text-dark">
                                <i class="fas fa-magic"></i>
                            </a>
                        </div>
                    `;
                }

                variantsCell.innerHTML = variantHTML;
            } else {
                variantsCell.innerHTML = '-';
            }

            // Tanggal pesanan
            row.querySelector('.order-date').textContent = formatDate(order.created_at);

            // Tombol aksi
            const actionsContainer = row.querySelector('.order-actions .btn-group-sm') || row.querySelector('.order-actions');
            actionsContainer.innerHTML = `
                <a href="/admin/orders/${order.id}" class="btn btn-info btn-sm" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/admin/orders/${order.id}/edit" class="btn btn-warning btn-sm" title="Edit Status">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="/admin/orders/${order.id}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            `;

            return row;
        }

        // Fungsi untuk memeriksa pesanan baru
        function checkNewOrders(orders) {
            // Cari pesanan dengan ID lebih besar dari lastOrderId
            const newOrders = orders.filter(order => order.id > lastOrderId);

            if (newOrders.length > 0) {
                // Play notification sound
                const notificationSound = document.getElementById('notification-sound');
                if (notificationSound) {
                    notificationSound.play().catch(err => {
                        console.log('Error playing notification sound:', err);
                    });
                }

                // Update lastOrderId
                lastOrderId = newOrders[0].id;

                // Show toast notification
                newOrderMessage.textContent = `${newOrders.length} pesanan baru telah diterima.`;

                // Clear previous items
                orderItemsPreview.innerHTML = '';

                // Add first 3 items to the preview
                const previewOrder = newOrders[0];
                if (previewOrder.order_items && previewOrder.order_items.length > 0) {
                    const maxItems = Math.min(3, previewOrder.order_items.length);
                    for (let i = 0; i < maxItems; i++) {
                        const item = previewOrder.order_items[i];
                        const productName = item.product ? item.product.name : 'Produk';

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'mb-1';

                        let variantBadge = '';
                        if (item.variant_type === 'hot') {
                            variantBadge = '<span class="badge bg-danger"><i class="fas fa-fire"></i></span>';
                        } else if (item.variant_type === 'ice') {
                            variantBadge = '<span class="badge bg-info"><i class="fas fa-snowflake"></i></span>';
                        }

                        itemDiv.innerHTML = `
                            <span>${productName}</span>
                            <span class="text-muted">x${item.quantity}</span>
                            ${variantBadge}
                        `;

                        orderItemsPreview.appendChild(itemDiv);
                    }

                    if (previewOrder.order_items.length > maxItems) {
                        const moreItems = document.createElement('div');
                        moreItems.className = 'mt-1 text-muted fst-italic';
                        moreItems.textContent = `...dan ${previewOrder.order_items.length - maxItems} item lainnya`;
                        orderItemsPreview.appendChild(moreItems);
                    }
                }

                toast.show();
            }
        }

        // Mulai timer jika auto refresh aktif
        if (autoRefreshEnabled) {
            startTimer();
        }
    });
</script>
