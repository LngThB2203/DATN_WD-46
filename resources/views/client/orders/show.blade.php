@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Thông tin sản phẩm --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Mã đơn hàng: <strong>#{{ str_pad($order->id,6,'0',STR_PAD_LEFT) }}</strong></h5>
                        @php
                            $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                            $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                    </div>
                    <div class="card-body">
                        <h6>Sản phẩm</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $reviewBtnShownForProducts = [];
                                    @endphp
                                    @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($detail->product && $detail->product->primaryImage())
                                                <img src="{{ asset('storage/'.$detail->product->primaryImage()->image_path) }}" alt="{{ $detail->product->name }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</strong>
                                                    @if($detail->variant)
                                                        <div class="small text-muted">
                                                            @if($detail->variant->size) Kích thước: {{ $detail->variant->size->size_name ?? $detail->variant->size->name ?? '' }} @endif
                                                            @if($detail->variant->scent) | Mùi: {{ $detail->variant->scent->scent_name ?? $detail->variant->scent->name ?? '' }} @endif
                                                            @if($detail->variant->concentration) | Nồng độ: {{ $detail->variant->concentration->concentration_name ?? $detail->variant->concentration->name ?? '' }} @endif
                                                        </div>
                                                    @else
                                                        <div class="small text-muted mt-1">Không có biến thể</div>
                                                    @endif
                                                    @php
                                                        $canReview = false;
                                                        if(auth()->check() && $detail->product) {
                                                            $user = auth()->user();
                                                            if($order->user_id === $user->id && \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status) === \App\Helpers\OrderStatusHelper::COMPLETED && $order->completed_at) {
                                                                if(\Carbon\Carbon::now()->diffInDays($order->completed_at) <= 15) {
                                                                    $alreadyReviewed = \App\Models\Review::where('user_id', $user->id)
                                                                        ->where('product_id', $detail->product->id)
                                                                        ->where('order_id', $order->id)
                                                                        ->exists();
                                                                    $canReview = ! $alreadyReviewed;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($canReview && !in_array($detail->product->id, $reviewBtnShownForProducts, true))
                                                        @php
                                                            $reviewBtnShownForProducts[] = $detail->product->id;
                                                        @endphp
                                                        <div class="mt-2">
                                                            <a href="{{ route('orders.review.form', [$order->id, $detail->product->id]) }}" class="btn btn-sm btn-outline-primary">
                                                                ⭐ Đánh giá sản phẩm
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($detail->price,0,',','.') }} đ</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->subtotal,0,',','.') }} đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Thông tin giao hàng --}}
                <div class="card mt-4">
                    <div class="card-header"><h5>Thông tin giao hàng</h5></div>
                    <div class="card-body">
                        @if(isset($canUpdateShipping) && $canUpdateShipping)
                            {{-- Form cập nhật thông tin --}}
                            <form method="POST" action="{{ route('orders.update-shipping', $order->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Họ tên</strong> <span class="text-danger">*</span></label>
                                    @if(auth()->check())
                                        <input type="text" name="customer_name" class="form-control" 
                                               value="{{ old('customer_name', auth()->user()->name) }}" readonly disabled>
                                        <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                                        <small class="text-muted">Thông tin từ tài khoản</small>
                                    @else
                                        <input type="text" name="customer_name" class="form-control" 
                                               value="{{ old('customer_name', $order->customer_name) }}" required>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email</strong></label>
                                    @if(auth()->check())
                                        <input type="email" name="customer_email" class="form-control" 
                                               value="{{ old('customer_email', auth()->user()->email) }}" readonly disabled>
                                        <input type="hidden" name="customer_email" value="{{ auth()->user()->email }}">
                                        <small class="text-muted">Thông tin từ tài khoản</small>
                                    @else
                                        <input type="email" name="customer_email" class="form-control" 
                                               value="{{ old('customer_email', $order->customer_email) }}">
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control" 
                                           value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Địa chỉ</strong> <span class="text-danger">*</span></label>
                                    <textarea name="shipping_address_line" class="form-control" rows="3" required>{{ old('shipping_address_line', $order->shipping_address_line ?? $order->shipping_address ?? '') }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Ghi chú</strong></label>
                                    <textarea name="customer_note" class="form-control" rows="2">{{ old('customer_note', $order->customer_note) }}</textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                            </form>
                        @else
                            {{-- Chỉ hiển thị thông tin (đã thanh toán hoặc không cho phép chỉnh sửa) --}}
                            <div class="mb-3">
                                @if(isset($isPaid) && $isPaid)
                                    <div class="alert alert-info">
                                        <small><i class="bi bi-info-circle"></i> Đơn hàng đã thanh toán, không thể thay đổi thông tin giao hàng.</small>
                                    </div>
                                @endif
                            </div>
                            <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                            @if($order->customer_email)<p><strong>Email:</strong> {{ $order->customer_email }}</p>@endif
                            <p><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->shipping_address_line ?? $order->shipping_address }}</p>
                            @if($order->shipping_province || $order->shipping_district || $order->shipping_ward)
                                <p><strong>Địa chỉ đầy đủ:</strong><br>
                                    {{ $order->shipping_address_line }},
                                    {{ $order->shipping_ward }},
                                    {{ $order->shipping_district }},
                                    {{ $order->shipping_province }}
                                </p>
                            @endif
                        @if($order->customer_note)<p><strong>Ghi chú:</strong> {{ $order->customer_note }}</p>@endif
                        @endif

                        <hr>

                        {{-- Nút hủy đơn --}}
                        @php
                            $mappedStatus = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
                            $canCancel = in_array($mappedStatus, [
                                \App\Helpers\OrderStatusHelper::PENDING, 
                                \App\Helpers\OrderStatusHelper::PREPARING
                            ]);
                        @endphp
                        @if($canCancel)
                        <button type="button" class="btn btn-danger mt-3 w-100" id="cancelOrderBtn">Hủy đơn hàng</button>

                        <!-- Cancel reason modal -->
                        <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <form id="cancelOrderForm" method="POST" action="{{ route('orders.cancel', $order->id) }}">
                              @csrf
                              @method('PUT')
                              <input type="hidden" name="cancellation_reason" id="cancellation_reason_input">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="cancelOrderModalLabel">Lý do hủy đơn hàng</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label class="form-label">Chọn lý do hủy (tùy chọn)</label>
                                    <div class="btn-group d-flex flex-column mb-2" role="group" aria-label="Cancel reasons">
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_wrong_address" value="Nhập sai địa chỉ / muốn thay đổi địa chỉ giao hàng">
                                        <label class="form-check-label" for="cancel_reason_wrong_address">Nhập sai địa chỉ / muốn thay đổi địa chỉ giao hàng</label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_change_items" value="Muốn thay đổi sản phẩm/số lượng">
                                        <label class="form-check-label" for="cancel_reason_change_items">Muốn thay đổi sản phẩm/số lượng</label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_finance" value="Tài chính / muốn hoãn lại">
                                        <label class="form-check-label" for="cancel_reason_finance">Tài chính / muốn hoãn lại</label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_price" value="Nhận được thông tin giá/chi phí chưa hợp lý">
                                        <label class="form-check-label" for="cancel_reason_price">Nhận được thông tin giá/chi phí chưa hợp lý</label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_no_need" value="Không còn nhu cầu">
                                        <label class="form-check-label" for="cancel_reason_no_need">Không còn nhu cầu</label>
                                      </div>
                                      <div class="form-check">
                                        <input class="form-check-input" type="radio" name="cancellation_reason_radio" id="cancel_reason_other" value="other">
                                        <label class="form-check-label" for="cancel_reason_other">Khác (ghi rõ bên dưới)</label>
                                      </div>
                                    </div>

                                    <label for="cancellation_reason" class="form-label">Ghi chi tiết (tùy chọn)</label>
                                    <textarea class="form-control" id="cancellation_reason" rows="3" placeholder="Nhập lý do..."></textarea>
                                    <small class="form-text text-muted">Bạn có thể chọn một lý do nhanh ở trên hoặc nhập chi tiết nếu chọn "Khác".</small>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                  <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>

                        <script>
                        (function(){
                            var btn = document.getElementById('cancelOrderBtn');
                            if (btn) {
                                btn.addEventListener('click', function(){
                                    var modalEl = document.getElementById('cancelOrderModal');
                                    if (modalEl && window.bootstrap) {
                                        var bs = new bootstrap.Modal(modalEl);
                                        bs.show();
                                    }
                                });
                            }

                            var form = document.getElementById('cancelOrderForm');
                            if (form) {
                                var reasonTa = document.getElementById('cancellation_reason');
                                var radios = document.querySelectorAll('input[name="cancellation_reason_radio"]');

                                if (radios && radios.length) {
                                    radios.forEach(function(r){
                                        r.addEventListener('change', function(){
                                            if (!reasonTa) return;
                                            if (this.value === 'other') {
                                                reasonTa.value = '';
                                                reasonTa.focus();
                                            } else {
                                                reasonTa.value = this.value;
                                            }
                                        });
                                    });
                                }

                                form.addEventListener('submit', function(){
                                    var reason = reasonTa ? reasonTa.value : '';
                                    document.getElementById('cancellation_reason_input').value = reason.trim();
                                });
                            }
                        })();
                        </script>
                        @endif

                        {{-- Nút xác nhận đã nhận hàng --}}
                        @if($mappedStatus === \App\Helpers\OrderStatusHelper::DELIVERED)
                        <form method="POST" action="{{ route('orders.confirm-received', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success mt-3 w-100">
                                Xác nhận đã nhận hàng
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5>Tóm tắt đơn hàng</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between"><span>Tạm tính</span><span>{{ number_format($order->subtotal ?? $order->total_price,0,',','.') }} đ</span></div>
                        @if($order->discount_total > 0)
                        <div class="d-flex justify-content-between text-success"><span>Giảm giá</span><span>-{{ number_format($order->discount_total,0,',','.') }} đ</span></div>
                        @endif
                        <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><span>{{ number_format($order->shipping_cost ?? 0,0,',','.') }} đ</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3"><span>Tổng cộng</span><span class="text-primary fs-5">{{ number_format($order->grand_total ?? $order->total_price,0,',','.') }} đ</span></div>

                        <strong>Phương thức thanh toán:</strong>
                        <p class="mb-0">
                            @if($order->payment_method === 'cod') Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method === 'bank_transfer') Chuyển khoản ngân hàng
                            @elseif($order->payment_method === 'online') Thanh toán online
                            @else {{ ucfirst(str_replace('_',' ',$order->payment_method)) }} @endif
                        </p>

                        <div class="mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
