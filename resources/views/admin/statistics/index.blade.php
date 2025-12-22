@extends('admin.layouts.admin')

@section('title', 'Thống kê')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Alert -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-primary" role="alert">
                    Dashboard thống kê doanh thu và sản phẩm bán chạy
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h6">Tổng doanh thu</div>
                        <div class="h3 fw-bold">{{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }} đ</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h6">Số đơn hàng đã thành công</div>
                        <div class="h3 fw-bold">{{ $summary['orders'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h6">Số SP đã bán</div>
                        <div class="h3 fw-bold">{{ $summary['items'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="h6">Email đăng ký</div>
                        <div class="h3 fw-bold">{{ $summary['newsletter_count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row g-3 mb-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label class="form-label d-block mb-1">Chế độ lọc</label>
                <div class="d-flex flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filterMode" id="modeDateRange" value="date_range" checked>
                        <label class="form-check-label" for="modeDateRange">Theo khoảng ngày</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filterMode" id="modeMonth" value="month">
                        <label class="form-check-label" for="modeMonth">Theo tháng</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filterMode" id="modeYear" value="year">
                        <label class="form-check-label" for="modeYear">Theo năm</label>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3 mb-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">Từ ngày</label>
                <input type="date" id="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-auto">
                <label class="form-label">Đến ngày</label>
                <input type="date" id="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-auto">
                <label class="form-label">Tháng</label>
                <select id="month" class="form-select">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int)now()->month === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Năm</label>
                <select id="year" class="form-select">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto d-flex flex-wrap gap-2">
                <div class="btn-group me-2 mb-2" role="group" aria-label="Quick filters">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="quickToday">Hôm nay</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="quick7Days">7 ngày gần nhất</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="quickThisMonth">Tháng này</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="quickLastMonth">Tháng trước</button>
                </div>
                <div class="d-flex gap-2 mb-2">
                    <button id="btnApply" class="btn btn-primary">Áp dụng</button>
                    <a id="btnExcel" class="btn btn-success" href="#">Xuất Excel</a>
                    <a id="btnPdf" class="btn btn-danger" href="#">Xuất PDF</a>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-2 mb-2">
            <div class="col-12">
                <small id="currentFilterInfo" class="text-muted"></small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">Biểu đồ doanh thu</div>
                    <div class="card-body">
                        <canvas id="revenueChart" height="140"></canvas>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Biểu đồ số đơn hàng</div>
                    <div class="card-body">
                        <canvas id="orderChart" height="140"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">Thống kê sản phẩm</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="fw-semibold mb-1 text-danger">Sắp hết hàng (&lt; 10)</div>
                            <div class="text-muted small mb-1">Số lượng tồn kho &lt; 10</div>
                            <ul id="lowStockProducts" class="list-group list-group-flush"></ul>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold mb-1 text-success">Bán chạy (Top 5)</div>
                            <div class="text-muted small mb-1">Top 5 sản phẩm có số lượng bán cao nhất trong khoảng thời gian đã chọn</div>
                            <ul id="bestSellerProducts" class="list-group list-group-flush"></ul>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold mb-1 text-warning">Bán chậm (&lt; 3)</div>
                            <div class="text-muted small mb-1">Tổng số lượng bán &gt; 0 và &lt; 3 trong khoảng thời gian đã chọn</div>
                            <ul id="slowProducts" class="list-group list-group-flush"></ul>
                        </div>
                        <div class="mb-3">
                            <div class="fw-semibold mb-1 text-primary">Đánh giá cao nhất (Top 5)</div>
                            <div class="text-muted small mb-1">Top 5 sản phẩm có điểm đánh giá trung bình cao nhất trong khoảng thời gian đã chọn</div>
                            <ul id="topRatedProducts" class="list-group list-group-flush"></ul>
                        </div>
                        <div>
                            <div class="fw-semibold mb-1 text-dark">Không bán được (0)</div>
                            <div class="text-muted small mb-1">Tổng số lượng bán = 0 nhưng vẫn còn tồn kho trong khoảng thời gian đã chọn</div>
                            <ul id="deadStockProducts" class="list-group list-group-flush"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const routes = {
    revenue: "{{ route('admin.statistics.revenue-data') }}",
    productStats: "{{ route('admin.statistics.product-stats') }}",
    excel: "{{ route('admin.statistics.export-excel') }}",
    pdf: "{{ route('admin.statistics.export-pdf') }}",
};

const productShowTemplate = "{{ route('products.show', ['product' => '__ID__']) }}";
const imageBaseUrl = "{{ asset('storage') }}/";

const qs = (p={}) => new URLSearchParams(p).toString();

const fromEl = document.getElementById('from');
const toEl = document.getElementById('to');
const monthEl = document.getElementById('month');
const yearEl = document.getElementById('year');
const btnApply = document.getElementById('btnApply');
const btnExcel = document.getElementById('btnExcel');
const btnPdf = document.getElementById('btnPdf');
const modeRadios = document.querySelectorAll('input[name="filterMode"]');
const filterInfoEl = document.getElementById('currentFilterInfo');

const quickTodayBtn = document.getElementById('quickToday');
const quick7DaysBtn = document.getElementById('quick7Days');
const quickThisMonthBtn = document.getElementById('quickThisMonth');
const quickLastMonthBtn = document.getElementById('quickLastMonth');

let revenueChart;
let orderChart;

function formatCurrencyVN(value){
    const v = Number(value || 0);
    if (v >= 1_000_000_000) {
        return (v / 1_000_000_000).toFixed(1).replace('.', ',') + ' tỷ';
    }
    if (v >= 1_000_000) {
        return (v / 1_000_000).toFixed(1).replace('.', ',') + ' triệu';
    }
    return v.toLocaleString('vi-VN');
}

function getCurrentMode(){
    let mode = 'date_range';
    modeRadios.forEach(r => { if (r.checked) mode = r.value; });
    return mode;
}

function setMode(mode){
    modeRadios.forEach(r => { r.checked = (r.value === mode); });
    applyModeConstraints();
}

function applyModeConstraints(){
    const mode = getCurrentMode();

    if (mode === 'date_range') {
        fromEl.disabled = false;
        toEl.disabled = false;
        monthEl.disabled = true;
        yearEl.disabled = true;
    } else if (mode === 'month') {
        fromEl.disabled = true;
        toEl.disabled = true;
        monthEl.disabled = false;
        yearEl.disabled = false;

        const y = parseInt(yearEl.value, 10) || new Date().getFullYear();
        const m = parseInt(monthEl.value, 10) || (new Date().getMonth() + 1);
        const first = new Date(y, m - 1, 1);
        const last = new Date(y, m, 0);
        fromEl.value = first.toISOString().slice(0, 10);
        toEl.value = last.toISOString().slice(0, 10);
    } else if (mode === 'year') {
        fromEl.disabled = true;
        toEl.disabled = true;
        monthEl.disabled = true;
        yearEl.disabled = false;

        const y = parseInt(yearEl.value, 10) || new Date().getFullYear();
        const first = new Date(y, 0, 1);
        const last = new Date(y, 11, 31);
        fromEl.value = first.toISOString().slice(0, 10);
        toEl.value = last.toISOString().slice(0, 10);
    }

    updateFilterInfoText();
}

function formatDateDisplay(iso){
    if (!iso) return '';
    const [y,m,d] = iso.split('-');
    return `${d}/${m}/${y}`;
}

function updateFilterInfoText(){
    if (!filterInfoEl) return;
    const mode = getCurrentMode();

    if (mode === 'date_range') {
        const from = formatDateDisplay(fromEl.value);
        const to = formatDateDisplay(toEl.value);
        filterInfoEl.textContent = from && to ? `Dữ liệu từ ${from} – ${to}` : '';
    } else if (mode === 'month') {
        const m = monthEl.value;
        const y = yearEl.value;
        filterInfoEl.textContent = m && y ? `Thống kê tháng ${m} / ${y}` : '';
    } else if (mode === 'year') {
        const y = yearEl.value;
        filterInfoEl.textContent = y ? `Thống kê năm ${y}` : '';
    }
}

async function loadRevenue(){
    const url = routes.revenue + '?' + qs({from: fromEl.value, to: toEl.value});
    const res = await fetch(url);
    const data = await res.json();

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const orderCtx = document.getElementById('orderChart').getContext('2d');

    if(revenueChart) revenueChart.destroy();
    if(orderChart) orderChart.destroy();

    revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {label: 'Doanh thu', data: data.revenue, backgroundColor: '#60a5fa'}
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => formatCurrencyVN(value)
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const label = ctx.dataset.label || '';
                            const v = ctx.parsed.y ?? 0;
                            return `${label}: ${formatCurrencyVN(v)} VNĐ`;
                        }
                    }
                }
            }
        }
    });

    orderChart = new Chart(orderCtx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {label: 'Số đơn', data: data.orders, borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.2)', tension: 0.3}
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {beginAtZero: true}
            }
        }
    });
}

async function loadProductStats(){
    const url = routes.productStats + '?' + qs({from: fromEl.value, to: toEl.value});
    const res = await fetch(url);
    const data = await res.json();

    const renderList = (elementId, rows, valueField, badgeClass = '') => {
        const ul = document.getElementById(elementId);
        if (!ul) return;
        ul.innerHTML = '';
        rows.forEach((r, i) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const imageUrl = r.image_path ? (imageBaseUrl + r.image_path) : '';
            const productUrl = productShowTemplate.replace('__ID__', r.id ?? '');

            li.innerHTML = `
                <span>
                    <a href="${productUrl}" class="product-stat-link" data-image="${imageUrl}">
                        ${i + 1}. ${r.name}
                    </a>
                </span>
                <span class="badge ${badgeClass}">${parseInt(r[valueField] ?? 0)}</span>
            `;
            ul.appendChild(li);
        });
        if (rows.length === 0) {
            const li = document.createElement('li');
            li.className = 'list-group-item text-muted';
            li.textContent = 'Không có dữ liệu';
            ul.appendChild(li);
        }
    };

    renderList('lowStockProducts', data.low_stock ?? [], 'stock_qty', 'bg-danger');
    renderList('bestSellerProducts', data.best_sellers ?? [], 'total_sold', 'bg-success');
    renderList('slowProducts', data.slow_moving ?? [], 'total_sold', 'bg-warning text-dark');

    const renderTopRated = (elementId, rows) => {
        const ul = document.getElementById(elementId);
        if (!ul) return;
        ul.innerHTML = '';
        rows.forEach((r, i) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const imageUrl = r.image_path ? (imageBaseUrl + r.image_path) : '';
            const productUrl = productShowTemplate.replace('__ID__', r.id ?? '');
            const avg = Number(r.avg_rating ?? 0).toFixed(2);
            const count = parseInt(r.reviews_count ?? 0);

            li.innerHTML = `
                <span>
                    <a href="${productUrl}" class="product-stat-link" data-image="${imageUrl}">
                        ${i + 1}. ${r.name}
                    </a>
                </span>
                <span class="badge bg-primary">${avg} ★ (${count})</span>
            `;
            ul.appendChild(li);
        });
        if (rows.length === 0) {
            const li = document.createElement('li');
            li.className = 'list-group-item text-muted';
            li.textContent = 'Không có dữ liệu';
            ul.appendChild(li);
        }
    };

    renderTopRated('topRatedProducts', data.top_rated ?? []);
    renderList('deadStockProducts', data.dead_stock ?? [], 'stock_qty', 'bg-secondary');

    setupImagePreviewHover();
}

let productPreviewEl;

function setupImagePreviewHover() {
    if (!productPreviewEl) {
        productPreviewEl = document.createElement('div');
        productPreviewEl.id = 'product-image-preview';
        productPreviewEl.style.position = 'fixed';
        productPreviewEl.style.display = 'none';
        productPreviewEl.style.zIndex = '9999';
        productPreviewEl.style.padding = '4px';
        productPreviewEl.style.background = 'rgba(0,0,0,0.7)';
        productPreviewEl.style.borderRadius = '4px';
        document.body.appendChild(productPreviewEl);
    }

    document.querySelectorAll('.product-stat-link').forEach(a => {
        const img = a.dataset.image;
        if (!img) return;

        a.addEventListener('mouseenter', (e) => {
            productPreviewEl.innerHTML = `<img src="${img}" alt="" style="max-width:120px;max-height:120px;object-fit:cover;">`;
            productPreviewEl.style.left = (e.clientX + 16) + 'px';
            productPreviewEl.style.top = (e.clientY + 16) + 'px';
            productPreviewEl.style.display = 'block';
        });

        a.addEventListener('mousemove', (e) => {
            productPreviewEl.style.left = (e.clientX + 16) + 'px';
            productPreviewEl.style.top = (e.clientY + 16) + 'px';
        });

        a.addEventListener('mouseleave', () => {
            productPreviewEl.style.display = 'none';
        });
    });
}

function bindExports(){
    const params = () => ({from: fromEl.value, to: toEl.value, month: monthEl.value, year: yearEl.value});
    btnExcel.href = routes.excel + '?' + qs(params());
    btnPdf.href = routes.pdf + '?' + qs(params());
}

btnApply.addEventListener('click', () => {
    // Đảm bảo from/to luôn được tính lại theo mode và giá trị tháng/năm hiện tại
    applyModeConstraints();
    loadRevenue();
    loadProductStats();
    bindExports();
});

modeRadios.forEach(r => {
    r.addEventListener('change', () => {
        applyModeConstraints();
        loadRevenue();
        loadProductStats();
        bindExports();
    });
});

quickTodayBtn.addEventListener('click', () => {
    const today = new Date();
    const iso = today.toISOString().slice(0, 10);
    setMode('date_range');
    fromEl.value = iso;
    toEl.value = iso;
    updateFilterInfoText();
    loadRevenue();
    loadProductStats();
    bindExports();
});

quick7DaysBtn.addEventListener('click', () => {
    const today = new Date();
    const past = new Date();
    past.setDate(today.getDate() - 6);
    setMode('date_range');
    fromEl.value = past.toISOString().slice(0, 10);
    toEl.value = today.toISOString().slice(0, 10);
    updateFilterInfoText();
    loadRevenue();
    loadProductStats();
    bindExports();
});

quickThisMonthBtn.addEventListener('click', () => {
    const now = new Date();
    const y = now.getFullYear();
    const m = now.getMonth() + 1;
    yearEl.value = y;
    monthEl.value = m;
    setMode('month');
    updateFilterInfoText();
    loadRevenue();
    loadProductStats();
    bindExports();
});

quickLastMonthBtn.addEventListener('click', () => {
    const now = new Date();
    let y = now.getFullYear();
    let m = now.getMonth(); // 0-11, last month
    if (m === 0) { m = 12; y = y - 1; } else { m = m; }
    yearEl.value = y;
    monthEl.value = m;
    setMode('month');
    updateFilterInfoText();
    loadRevenue();
    loadProductStats();
    bindExports();
});

applyModeConstraints();
loadRevenue();
loadProductStats();
bindExports();
</script>
@endpush
