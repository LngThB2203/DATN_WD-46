@extends('admin.layouts.admin')

@section('title', 'Thống kê')

@section('content')
<div class="container-fluid p-4">
  <div class="d-flex flex-wrap gap-2 align-items-end mb-3">
    <div>
      <label class="form-label">Từ ngày</label>
      <input type="date" id="from" class="form-control" value="{{ $from }}">
    </div>
    <div>
      <label class="form-label">Đến ngày</label>
      <input type="date" id="to" class="form-control" value="{{ $to }}">
    </div>
    <div>
      <label class="form-label">Tháng</label>
      <select id="month" class="form-select">
        @for ($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" {{ (int)now()->month === $m ? 'selected' : '' }}>{{ $m }}</option>
        @endfor
      </select>
    </div>
    <div>
      <label class="form-label">Năm</label>
      <select id="year" class="form-select">
        @for ($y = now()->year; $y >= now()->year - 5; $y--)
          <option value="{{ $y }}">{{ $y }}</option>
        @endfor
      </select>
    </div>
    <button id="btnApply" class="btn btn-primary">Áp dụng</button>
    <a id="btnExcel" class="btn btn-success" href="#">Xuất Excel</a>
    <a id="btnPdf" class="btn btn-danger" href="#">Xuất PDF</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6">Tổng doanh thu</div>
          <div class="h3 fw-bold">{{ number_format($summary['revenue'] ?? 0, 0, ',', '.') }} đ</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6">Số đơn hàng</div>
          <div class="h3 fw-bold">{{ $summary['orders'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="h6">Số SP đã bán</div>
          <div class="h3 fw-bold">{{ $summary['items'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">Biểu đồ doanh thu</div>
        <div class="card-body"><canvas id="revenueChart" height="120"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">Top sản phẩm theo tháng</div>
        <div class="card-body">
          <ul id="topProducts" class="list-group"></ul>
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
  top: "{{ route('admin.statistics.top-products') }}",
  excel: "{{ route('admin.statistics.export-excel') }}",
  pdf: "{{ route('admin.statistics.export-pdf') }}",
};

const qs = (p={}) => new URLSearchParams(p).toString();

const fromEl = document.getElementById('from');
const toEl = document.getElementById('to');
const monthEl = document.getElementById('month');
const yearEl = document.getElementById('year');
const btnApply = document.getElementById('btnApply');
const btnExcel = document.getElementById('btnExcel');
const btnPdf = document.getElementById('btnPdf');

let chart;

async function loadRevenue(){
  const url = routes.revenue + '?' + qs({from: fromEl.value, to: toEl.value});
  const res = await fetch(url);
  const data = await res.json();
  const ctx = document.getElementById('revenueChart').getContext('2d');
  if(chart) chart.destroy();
  chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [
        {label: 'Doanh thu', data: data.revenue, backgroundColor: '#60a5fa'},
        {label: 'Số đơn', data: data.orders, type: 'line', borderColor: '#f87171', yAxisID: 'y2'}
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {beginAtZero: true},
        y2: {beginAtZero: true, position: 'right', grid:{drawOnChartArea:false}}
      }
    }
  });
}

async function loadTop(){
  const url = routes.top + '?' + qs({month: monthEl.value, year: yearEl.value});
  const res = await fetch(url);
  const rows = await res.json();
  const ul = document.getElementById('topProducts');
  ul.innerHTML = '';
  rows.forEach((r, i)=>{
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `<span>${i+1}. ${r.product_name}</span><span class="badge text-bg-primary">${parseInt(r.total_qty)}</span>`;
    ul.appendChild(li);
  })
}

function bindExports(){
  const params = () => ({from: fromEl.value, to: toEl.value, month: monthEl.value, year: yearEl.value});
  btnExcel.href = routes.excel + '?' + qs(params());
  btnPdf.href = routes.pdf + '?' + qs(params());
}

btnApply.addEventListener('click', ()=>{ loadRevenue(); loadTop(); bindExports(); });

loadRevenue();
loadTop();
bindExports();
</script>
@endpush
