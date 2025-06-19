@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    /* Gaya untuk tampilan kompak dashboard */
    .dashboard-widget {
        border-radius: 0.375rem;
        padding: 0.75rem;
        height: 100%;
    }
    .card-header {
        padding: 0.5rem 0.75rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .table th, .table td {
        padding: 0.4rem 0.5rem;
        font-size: 0.85rem;
    }
    .table thead th {
        white-space: nowrap;
    }
    .list-group-item {
        padding: 0.5rem 0.75rem;
    }
    h4, h5 {
        font-size: 1.1rem;
    }
    h4.widget-value {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }
    .mb-4 {
        margin-bottom: 0.75rem !important;
    }
    .mb-3 {
        margin-bottom: 0.5rem !important;
    }
    .small-icon {
        font-size: 0.8rem;
    }
    .widget-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
    .row {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    .col-xl-3, .col-md-6, .col-lg-8, .col-lg-4 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    .activity-description {
        margin-bottom: 0.25rem !important;
        font-size: 0.85rem;
    }
    .activity-time {
        font-size: 0.75rem;
    }
    .fa-stack {
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="row g-2">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-widget widget-primary">
            <div class="widget-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h4 id="totalOrders" class="widget-value">{{ $totalOrders ?? 0 }}</h4>
            <p class="mb-0 small">Total Pesanan</p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-widget widget-info">
            <div class="widget-icon">
                <i class="fas fa-mug-hot"></i>
            </div>
            <h4 class="widget-value">{{ $totalProducts ?? 0 }}</h4>
            <p class="mb-0 small">Total Produk</p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-widget widget-success">
            <div class="widget-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h4 id="totalRevenue" class="widget-value">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</h4>
            <p class="mb-0 small">Pendapatan</p>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-widget widget-warning">
            <div class="widget-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <h4 class="widget-value">{{ $totalCategories ?? 0 }}</h4>
            <p class="mb-0 small">Kategori</p>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-lg-8 mb-3">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pesanan Terbaru</h5>
                <div>
                    <div class="form-check form-switch d-inline-block me-2">
                        <input class="form-check-input" type="checkbox" id="autoRefreshToggle" checked>
                        <label class="form-check-label small" for="autoRefreshToggle">Auto</label>
                    </div>
                    <span id="refreshStatus" class="badge bg-success me-2">
                        <i class="fas fa-sync-alt fa-spin small-icon"></i>
                        <span id="refreshTimeLeft">5</span>s
                    </span>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Lihat</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td class="small">{{ $order->order_number }}</td>
                                <td class="small">{{ $order->customer_name ?? 'Anonim' }}</td>
                                <td class="small text-end">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="text-center">
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
                                <td class="small">{{ $order->created_at->format('d/m/y H:i') }}</td>
                            </tr>
                            @empty
                            <tr id="noOrdersRow">
                                <td colspan="5" class="text-center py-3">
                                    <i class="fas fa-clipboard-list fa-2x mb-2 text-muted"></i>
                                    <p class="small mb-0">Belum ada pesanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Produk Terlaris</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topProductsThisMonth ?? [] as $product)
                    <li class="list-group-item py-2">
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-center" style="width: 24px;">
                                <span class="badge bg-primary rounded-circle">{{ $loop->iteration }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="small fw-medium">{{ $product->name }}</span>
                                    <span class="text-success small">{{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-muted small">{{ $product->total_quantity }} terjual</div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item py-3 text-center">
                        <i class="fas fa-mug-hot fa-2x mb-2 text-muted"></i>
                        <p class="small mb-0">Belum ada produk terlaris</p>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="activitiesList">
                    @forelse($activities ?? [] as $activity)
                    <div class="list-group-item py-2 border-start-0 border-end-0">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <span class="fa-stack">
                                    <i class="fas fa-circle fa-stack-2x text-{{ $activity->type == 'order' ? 'success' : ($activity->type == 'product' ? 'primary' : ($activity->type == 'category' ? 'warning' : 'info')) }}"></i>
                                    <i class="fas fa-{{ $activity->type == 'order' ? 'shopping-cart' : ($activity->type == 'product' ? 'coffee' : ($activity->type == 'category' ? 'tag' : 'info-circle')) }} fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-1 activity-description">{{ $activity->description }}</p>
                                <small class="text-muted activity-time">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-3" id="noActivitiesRow">
                        <i class="fas fa-history fa-2x mb-2 text-muted"></i>
                        <p class="small mb-0">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-lg-8 mb-3">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Statistik {{ $currentMonthName }}</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" id="showSalesBtn">Pesanan</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showRevenueBtn">Pendapatan</button>
                </div>
            </div>
            <div class="card-body" style="height: 250px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Statistik Bulanan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th class="text-center">Order</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlySalesStats ?? [] as $stat)
                            <tr>
                                <td class="small">{{ $stat['month'] }}</td>
                                <td class="text-center small">{{ $stat['order_count'] }}</td>
                                <td class="text-end small">{{ number_format($stat['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-2 small">Belum ada data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk baris pesanan baru -->
<template id="orderRowTemplate">
    <tr>
        <td class="small order-number"></td>
        <td class="small customer-name"></td>
        <td class="small text-end total-amount"></td>
        <td class="text-center order-status"></td>
        <td class="small order-date"></td>
    </tr>
</template>

<!-- Template untuk aktivitas baru -->
<template id="activityTemplate">
    <div class="list-group-item py-2 border-start-0 border-end-0">
        <div class="d-flex align-items-center">
            <div class="me-2">
                <span class="fa-stack">
                    <i class="fas fa-circle fa-stack-2x activity-icon-bg"></i>
                    <i class="fas activity-icon fa-stack-1x fa-inverse"></i>
                </span>
            </div>
            <div>
                <p class="mb-1 activity-description"></p>
                <small class="text-muted activity-time"></small>
            </div>
        </div>
    </div>
</template>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="newOrderToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white py-1">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto">Pesanan Baru</strong>
            <small>Baru saja</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body py-2 small">
            <span id="newOrderMessage"></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variabel untuk polling
        let autoRefreshEnabled = true;
        let refreshInterval = 5; // dalam detik, diubah dari 30 detik menjadi 5 detik
        let activitiesRefreshInterval = 10; // dalam detik
        let currentTimer = refreshInterval;
        let activitiesTimer = activitiesRefreshInterval;
        let timerInterval;
        let activitiesTimerInterval;
        let lastOrderId = 0;
        let lastActivityId = 0;
        let currentChartType = 'sales'; // sales atau revenue
        const timezone = "{{ \App\Models\SiteSetting::getValue('store_timezone', 'WIB') }}";

        // Elemen DOM
        const autoRefreshToggle = document.getElementById('autoRefreshToggle');
        const refreshStatus = document.getElementById('refreshStatus');
        const refreshTimeLeft = document.getElementById('refreshTimeLeft');
        const recentOrdersTable = document.getElementById('recentOrdersTable');
        const activitiesList = document.getElementById('activitiesList');
        const orderRowTemplate = document.getElementById('orderRowTemplate');
        const activityTemplate = document.getElementById('activityTemplate');
        const newOrderToast = document.getElementById('newOrderToast');
        const newOrderMessage = document.getElementById('newOrderMessage');
        const showSalesBtn = document.getElementById('showSalesBtn');
        const showRevenueBtn = document.getElementById('showRevenueBtn');

        // Inisialisasi toast
        const toast = new bootstrap.Toast(newOrderToast);

        // Data untuk chart
        const dailySales = @json(array_values($dailySales));
        const dailyRevenue = @json(array_values($dailyRevenue));
        const daysInMonth = {{ $daysInMonth }};
        const days = Array.from({length: daysInMonth}, (_, i) => i + 1);

        // Inisialisasi chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: dailySales,
                    backgroundColor: 'rgba(106, 52, 18, 0.2)',
                    borderColor: '#6a3412',
                    borderWidth: 2,
                    pointBackgroundColor: '#6a3412',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                if (currentChartType === 'sales') {
                                    return `Pesanan: ${context.raw}`;
                                } else {
                                    return `Pendapatan: Rp ${formatNumber(context.raw)}`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            borderDash: [2, 4],
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Toggle auto refresh
        autoRefreshToggle.addEventListener('change', function() {
            autoRefreshEnabled = this.checked;
            if (autoRefreshEnabled) {
                startTimer();
                startActivitiesTimer();
                refreshStatus.classList.remove('bg-secondary');
                refreshStatus.classList.add('bg-success');
            } else {
                stopTimer();
                stopActivitiesTimer();
                refreshStatus.classList.remove('bg-success');
                refreshStatus.classList.add('bg-secondary');
            }
        });

        // Toggle chart type
        showSalesBtn.addEventListener('click', function() {
            if (currentChartType !== 'sales') {
                currentChartType = 'sales';
                updateChartData(dailySales, 'Jumlah Pesanan');
                showSalesBtn.classList.add('active');
                showRevenueBtn.classList.remove('active');
            }
        });

        showRevenueBtn.addEventListener('click', function() {
            if (currentChartType !== 'revenue') {
                currentChartType = 'revenue';
                updateChartData(dailyRevenue, 'Pendapatan (Rp)');
                showRevenueBtn.classList.add('active');
                showSalesBtn.classList.remove('active');
            }
        });

        // Fungsi untuk memulai timer
        function startTimer() {
            stopTimer();
            timerInterval = setInterval(() => {
                currentTimer--;
                refreshTimeLeft.textContent = currentTimer;

                if (currentTimer <= 0) {
                    fetchDashboardData();
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

        // Fungsi untuk mengambil data dashboard terbaru
        function fetchDashboardData() {
            fetch('{{ route("admin.api.dashboard-data") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateDashboard(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching dashboard data:', error);
                });
        }

        // Fungsi untuk memperbarui dashboard
        function updateDashboard(data) {
            // Update total orders
            document.getElementById('totalOrders').textContent = data.totalOrders;

            // Update total revenue
            document.getElementById('totalRevenue').textContent = formatNumber(data.totalRevenue);

            // Update recent orders
            updateRecentOrders(data.recentOrders);

            // Update chart data
            if (currentChartType === 'sales') {
                updateChartData(data.dailySales, 'Jumlah Pesanan');
            } else {
                updateChartData(data.dailyRevenue, 'Pendapatan (Rp)');
            }

            // Check for new orders
            checkNewOrders(data.recentOrders);
        }

        // Fungsi untuk memperbarui tabel pesanan terbaru
        function updateRecentOrders(orders) {
            if (orders.length === 0) {
                if (!document.getElementById('noOrdersRow')) {
                    recentOrdersTable.innerHTML = `
                        <tr id="noOrdersRow">
                            <td colspan="5" class="text-center py-3">
                                <i class="fas fa-clipboard-list fa-2x mb-2 text-muted"></i>
                                <p class="small mb-0">Belum ada pesanan</p>
                            </td>
                        </tr>
                    `;
                }
                return;
            }

            // Hapus pesan "tidak ada pesanan" jika ada
            const noOrdersRow = document.getElementById('noOrdersRow');
            if (noOrdersRow) {
                noOrdersRow.remove();
            }

            // Kosongkan tabel dan tambahkan pesanan baru
            recentOrdersTable.innerHTML = '';
            orders.forEach(order => {
                const row = orderRowTemplate.content.cloneNode(true);

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

                // Tanggal pesanan
                const date = new Date(order.created_at);
                const formattedDate = `${date.getDate()}/${(date.getMonth()+1).toString().padStart(2, '0')}/${date.getFullYear().toString().slice(-2)} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
                row.querySelector('.order-date').textContent = formattedDate;

                recentOrdersTable.appendChild(row);
            });
        }

        // Fungsi untuk memperbarui daftar aktivitas
        function updateActivities(activities) {
            if (activities.length === 0) {
                if (!document.getElementById('noActivitiesRow')) {
                    activitiesList.innerHTML = `
                        <div class="list-group-item text-center py-3" id="noActivitiesRow">
                            <i class="fas fa-history fa-2x mb-2 text-muted"></i>
                            <p class="small mb-0">Belum ada aktivitas</p>
                        </div>
                    `;
                }
                return;
            }

            // Hapus pesan "tidak ada aktivitas" jika ada
            const noActivitiesRow = document.getElementById('noActivitiesRow');
            if (noActivitiesRow) {
                noActivitiesRow.remove();
            }

            // Kosongkan daftar dan tambahkan aktivitas baru
            activitiesList.innerHTML = '';
            activities.forEach(activity => {
                const item = activityTemplate.content.cloneNode(true);

                // Set icon berdasarkan tipe aktivitas
                const iconBg = item.querySelector('.activity-icon-bg');
                const icon = item.querySelector('.activity-icon');

                if (activity.type === 'order') {
                    iconBg.classList.add('text-success');
                    icon.classList.add('fa-shopping-cart');
                } else if (activity.type === 'product') {
                    iconBg.classList.add('text-primary');
                    icon.classList.add('fa-coffee');
                } else if (activity.type === 'category') {
                    iconBg.classList.add('text-warning');
                    icon.classList.add('fa-tag');
                } else {
                    iconBg.classList.add('text-info');
                    icon.classList.add('fa-info-circle');
                }

                // Set deskripsi dan waktu
                item.querySelector('.activity-description').textContent = activity.description;

                const date = new Date(activity.created_at);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);

                let timeText;
                if (diffInSeconds < 60) {
                    timeText = 'Baru saja';
                } else if (diffInSeconds < 3600) {
                    timeText = `${Math.floor(diffInSeconds / 60)} menit yang lalu`;
                } else if (diffInSeconds < 86400) {
                    timeText = `${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
                } else {
                    timeText = `${Math.floor(diffInSeconds / 86400)} hari yang lalu`;
                }

                item.querySelector('.activity-time').textContent = timeText;

                activitiesList.appendChild(item);
            });
        }

        // Fungsi untuk memformat angka
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Mulai timer jika auto refresh diaktifkan
        if (autoRefreshEnabled) {
            startTimer();
            startActivitiesTimer();
        }

        // Fungsi untuk memperbarui data chart
        function updateChartData(data, label) {
            salesChart.data.datasets[0].data = data;
            salesChart.data.datasets[0].label = label;
            salesChart.update();
        }

        // Fungsi untuk memeriksa pesanan baru
        function checkNewOrders(orders) {
            if (orders.length > 0) {
                const newestOrderId = orders[0].id;
                if (lastOrderId > 0 && newestOrderId > lastOrderId) {
                    const newOrders = orders.filter(order => order.id > lastOrderId);
                    if (newOrders.length > 0) {
                        const count = newOrders.length;
                        newOrderMessage.textContent = `${count} pesanan baru telah diterima!`;
                        toast.show();

                        // Mainkan suara notifikasi
                        playNotificationSound();
                    }
                }
                lastOrderId = newestOrderId;
            }
        }

        // Fungsi untuk memainkan suara notifikasi
        function playNotificationSound() {
            const audio = new Audio('/audio/notification.mp3');
            audio.play().catch(error => {
                console.error('Error playing notification sound:', error);
            });
        }

        // Fungsi untuk mendapatkan nama bulan
        function getMonthName(monthIndex) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return months[monthIndex];
        }

        // Style untuk highlight baris baru
        const style = document.createElement('style');
        style.textContent = `
            .highlight-new {
                animation: highlightFade 3s;
            }

            @keyframes highlightFade {
                0% { background-color: rgba(76, 175, 80, 0.3); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);

        // Mulai timer untuk aktivitas
        function startActivitiesTimer() {
            stopActivitiesTimer();
            activitiesTimerInterval = setInterval(() => {
                activitiesTimer--;

                if (activitiesTimer <= 0) {
                    fetchActivitiesData();
                    resetActivitiesTimer();
                }
            }, 1000);
        }

        // Fungsi untuk menghentikan timer aktivitas
        function stopActivitiesTimer() {
            clearInterval(activitiesTimerInterval);
        }

        // Fungsi untuk mengatur ulang timer aktivitas
        function resetActivitiesTimer() {
            activitiesTimer = activitiesRefreshInterval;
        }

        // Fungsi untuk mengambil data aktivitas terbaru
        function fetchActivitiesData() {
            fetch('{{ route("admin.api.activities-data") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateActivities(data.data.activities);
                        checkNewActivities(data.data.activities);
                    }
                })
                .catch(error => {
                    console.error('Error fetching activities data:', error);
                });
        }

        // Fungsi untuk memeriksa aktivitas baru
        function checkNewActivities(activities) {
            if (activities.length > 0 && lastActivityId > 0) {
                const newestActivityId = activities[0].id;
                if (newestActivityId > lastActivityId) {
                    // Highlight aktivitas baru
                    const newActivities = activities.filter(activity => activity.id > lastActivityId);
                    if (newActivities.length > 0) {
                        const activityItems = document.querySelectorAll('#activitiesList .list-group-item');
                        for (let i = 0; i < Math.min(newActivities.length, activityItems.length); i++) {
                            activityItems[i].classList.add('highlight-new');
                            setTimeout(() => {
                                activityItems[i].classList.remove('highlight-new');
                            }, 3000);
                        }
                    }
                }
                lastActivityId = newestActivityId;
            } else if (activities.length > 0) {
                lastActivityId = activities[0].id;
            }
        }
    });
</script>
@endsection
