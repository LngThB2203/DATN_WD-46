@extends('client.layouts.client')

@section('title', 'Trang chủ')

@section('content')
<main class="main">
<style>
/* Hide initial voucher bars to prevent flicker; JS will convert them into floating toasts */
.voucher-notification{opacity:0;visibility:hidden}
</style>
<div class="notifications">
    @auth
        @foreach(auth()->user()->notifications as $notification)
                @php
                    $message = $notification->data['message'] ?? '';
                    preg_match('/Voucher "(.*?)"/', $message, $matches);
                    $code = $matches[1] ?? '';
                @endphp
            @if($code)
                <div class="alert alert-info voucher-notification hero-voucher alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert" style="z-index:1050; min-width:160px; max-width:320px; border-radius:8px;">
                    <div class="toast-icon" aria-hidden="true"><i class="bi bi-gift-fill"></i></div>
                    <div class="voucher-body">
                        <strong class="voucher-title">{{ $notification->data['title'] ?? '' }}</strong>
                        <div class="voucher-msg" title="{{ $notification->data['message'] ?? '' }}">{{ $notification->data['message'] ?? '' }}</div>
                        <small class="voucher-time text-muted">({{ $notification->created_at->diffForHumans() }})</small>
                    </div>
                    <div class="actions d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary copy-code-btn" onclick="copyToClipboard(this, '{{ $code }}')" aria-label="Sao chép mã">
                            <i class="bi bi-clipboard"></i>
                        </button>
                        <button type="button" class="btn-close ms-1" data-bs-dismiss="alert" aria-label="Đóng"></button>
                    </div>
                </div>
            @else
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ $notification->data['title'] ?? '' }}:</strong>
                        {{ $notification->data['message'] ?? '' }}
                        <small class="text-muted">({{ $notification->created_at->diffForHumans() }})</small>
                    </div>
                </div>
            @endif
        @endforeach
    @endauth
</div>



    <!-- Hero Section -->
    <section class="hero py-5">
        <div class="container-fluid container-xl">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-3">Cửa hàng nước hoa trực tuyến</h2>
                    <p class="lead mb-4">Khám phá bộ sưu tập nước hoa chính hãng với ưu đãi hấp dẫn. Giao nhanh, đổi trả dễ dàng.</p>
                    <a href="#" class="btn btn-primary btn-lg">Mua ngay</a>
                </div>
                <div class="col-lg-6">
                    @if($heroBanner && $heroBanner->image)
                    <img class="img-fluid rounded" src="{{ asset('storage/' . $heroBanner->image) }}" alt="Hero">
                    @else
                    <img class="img-fluid rounded" src="{{ asset('assets/client/img/default-hero.webp') }}" alt="Hero">
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
<section class="py-5 border-top">
    <div class="container-fluid container-xl">
        <h3 class="fw-semibold mb-4">Sản phẩm nổi bật</h3>

        <div class="row g-4" id="productList">
            @foreach($products as $product)
            @php
                $img = $product->galleries->where('is_primary', true)->first() ?? $product->galleries->first();
                $imgUrl = $img ? asset('storage/'.$img->image_path) : asset('assets/client/img/product/product-1.webp');
            @endphp

            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                <div class="card h-100 position-relative">
                    <img src="{{ $imgUrl }}" class="card-img-top"
                        style="height:250px; object-fit:cover; border:1px solid #dee2e6; border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1">{{ $product->name }}</h5>

                        @if($product->variants->count())
                        <div class="variant-popup border p-2 bg-white shadow position-absolute top-50 start-50 translate-middle d-none"
                            style="z-index:10; width:90%; max-width:250px;">
                            <select class="form-select variant-select mb-2" data-product-id="{{ $product->id }}">
                                <option value="">Chọn biến thể</option>
                                @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}" data-price="{{ $variant->price }}">
                                    {{ $variant->size->size_name ?? '' }} {{ $variant->scent->scent_name ?? '' }}
                                </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary w-100 mb-2 confirm-add-btn" data-product-id="{{ $product->id }}">
                                <i class="bi bi-cart3"></i> Thêm vào giỏ
                            </button>
                            <button class="btn btn-secondary w-100 close-popup-btn">Hủy</button>
                        </div>
                        @endif

                        <div class="mt-auto">
                            <div class="product-price mb-2">
                                <span class="text-primary fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-outline-secondary btn-sm add-to-cart-btn"
                                    data-product-id="{{ $product->id }}">
                                    <i class="bi bi-cart3"></i>
                                </button>
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>

    </div>
</section>


</main>

<script>
document.addEventListener('DOMContentLoaded', function(){

    // Hiển thị popup biến thể
    document.querySelectorAll('.add-to-cart-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const popup = this.closest('.card-body').querySelector('.variant-popup');
            if(popup){
                popup.classList.remove('d-none');
            } else {
                ajaxAddToCart(productId, null);
            }
        });
    });

    // Đóng popup
    document.querySelectorAll('.close-popup-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const popup = this.closest('.variant-popup');
            popup.classList.add('d-none');
        });
    });

    // Thêm vào giỏ từ popup
    document.querySelectorAll('.confirm-add-btn').forEach(btn=>{
        btn.addEventListener('click', function(){
            const productId = this.dataset.productId;
            const select = this.closest('.variant-popup').querySelector('.variant-select');
            const variantId = select.value;
            if(!variantId){
                alert('Vui lòng chọn biến thể trước khi thêm vào giỏ');
                return;
            }
            ajaxAddToCart(productId, variantId);
            this.closest('.variant-popup').classList.add('d-none');
        });
    });

    // Cập nhật giá khi chọn biến thể
    document.querySelectorAll('.variant-select').forEach(select=>{
        select.addEventListener('change', function(){
            const variantPrice = this.selectedOptions[0]?.dataset?.price ?? null;
            const productId = this.dataset.productId;
            const priceDiv = document.getElementById('price-'+productId);
            if(variantPrice){
                priceDiv.innerHTML = '<span class="text-primary fw-bold">'+parseInt(variantPrice).toLocaleString('vi-VN')+' VNĐ</span>';
            }
        });
    });

    // Hàm AJAX add to cart
    function ajaxAddToCart(productId, variantId){
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':'application/json'
            },
            body: new URLSearchParams({
                product_id: productId,
                variant_id: variantId,
                quantity: 1
            })
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.success){
                showNotification(data.message,'success');
                updateCartBadge(data.cart_count);
            }else{
                showNotification(data.message,'error');
            }
        });
    }

    // Notification
    function showNotification(msg,type){
        const alertClass = type=='success'?'alert-success':'alert-danger';
        const div = document.createElement('div');
        div.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        div.style.cssText = 'top:20px; right:20px; z-index:9999; min-width:300px';
        div.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(div);
        setTimeout(()=>{div.remove()},3000);
    }

    // Tự động ẩn các thông báo voucher sau 20 giây (hỗ trợ cả thông báo được thêm sau khi trang tải xong)
    const scheduleVoucherHide = (el, delay = 10000) => {
        if(!el || el.dataset.voucherHideScheduled) return;
        el.dataset.voucherHideScheduled = '1';

        // Thêm CSS fade-out một lần vào <head>
        if(!document.getElementById('voucher-hide-style')){
            const style = document.createElement('style');
            style.id = 'voucher-hide-style';
            style.textContent = '.voucher-notification.fade-out{opacity:0;transform:translateY(-10px);transition:opacity .6s ease, transform .6s ease;}';
            document.head.appendChild(style);
        }

        setTimeout(() => {
            try {
                el.classList.add('fade-out');
                el.addEventListener('transitionend', ()=> el.remove(), { once: true });
                // fallback: remove nếu transition không xảy ra
                setTimeout(()=> { if(document.body.contains(el)) el.remove(); }, 1000);
            } catch(e) {
                if(el && el.remove) el.remove();
            }
        }, delay);
    };

    // Existing voucher elements will be aggregated into the single floating toast; no per-element hide scheduling needed.

    // Chuyển các thông báo voucher sang dạng nổi kiểu Shopee (floating toasts)
    // Tạo container nổi (stack từ dưới lên) và style cho các toasts (1 lần)
    if(!document.getElementById('voucher-floating-container')){
        const container = document.createElement('div');
        container.id = 'voucher-floating-container';
        container.style.cssText = 'position:fixed;bottom:20px;left:20px;display:flex;flex-direction:column-reverse;gap:10px;z-index:1060;pointer-events:none;max-width:100%';
        document.body.appendChild(container);

        const style = document.createElement('style');
        style.id = 'hero-voucher-style';
        style.textContent = `
/* Floating voucher (Shopee-like) — refined */
#voucher-floating-container{display:flex;flex-direction:column-reverse;gap:10px;pointer-events:none}
.voucher-floating{pointer-events:auto;display:flex;align-items:center;gap:10px;min-width:160px;max-width:320px;padding:10px 12px;background:linear-gradient(180deg,#ffffff 0%, #fbfbfd 100%);border-left:4px solid var(--accent,#ff8a00);border-radius:12px;border:1px solid rgba(0,0,0,0.04);box-shadow:0 10px 30px rgba(0,0,0,0.12);font-size:13px;color:#222;transform:translateX(-18px);opacity:0;visibility:hidden;transition:transform .36s cubic-bezier(.2,.8,.2,1),opacity .36s,box-shadow .2s;overflow:hidden}
.voucher-floating.show{transform:translateX(0);opacity:1;visibility:visible}
.voucher-floating:hover{transform:translateX(0) translateY(-3px);box-shadow:0 18px 40px rgba(0,0,0,0.18)}
.voucher-floating .toast-icon{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex:0 0 44px;background:linear-gradient(135deg,#ff9a00,#ff6f61);color:#fff;font-size:18px;box-shadow:0 6px 18px rgba(255,111,97,0.18)}
.voucher-floating .voucher-body{flex:1;min-width:0;overflow:hidden}
.voucher-floating .voucher-title{font-weight:700;font-size:13px;display:block;color:#111}
.voucher-floating .voucher-msg{display:block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:#444;margin-top:2px}
.voucher-floating .voucher-time{display:block;font-size:11px;color:#777;margin-top:6px}
.voucher-floating .actions{display:flex;gap:6px;align-items:center}
.voucher-floating .copy-code-btn{background:transparent;border:1px solid rgba(0,0,0,0.06);padding:6px 8px;border-radius:8px;color:#333;display:flex;align-items:center;justify-content:center;transition:background .18s,color .18s,transform .08s}
.voucher-floating .copy-code-btn:hover{background:var(--accent,#ff8a00);color:#fff;transform:translateY(-1px);border-color:transparent}
.voucher-floating .copy-code-btn.copied{background:#28a745;color:#fff;border-color:transparent}
.copy-code-btn.copied{background:#28a745;color:#fff;border-color:transparent}
.voucher-floating .copy-code-btn i{font-size:14px}
.voucher-floating .btn-close{background:transparent;border:none;padding:6px;border-radius:6px;color:#888}

/* Aggregate single toast + expanded list */
.voucher-aggregate{pointer-events:auto;display:flex;align-items:center;gap:10px;min-width:160px;max-width:320px;padding:10px 12px;background:linear-gradient(180deg,#ffffff 0%, #fbfbfd 100%);border-left:4px solid var(--accent,#ff8a00);border-radius:12px;border:1px solid rgba(0,0,0,0.04);box-shadow:0 10px 30px rgba(0,0,0,0.12);font-size:13px;color:#222;cursor:pointer;transform:translateX(-18px);opacity:0;visibility:hidden;transition:transform .36s cubic-bezier(.2,.8,.2,1),opacity .36s}
.voucher-aggregate.show{transform:translateX(0);opacity:1;visibility:visible}
.voucher-aggregate .agg-badge{background:rgba(0,0,0,0.06);padding:4px 8px;border-radius:999px;font-weight:700;font-size:12px}
.voucher-aggregate .agg-title{font-weight:700;font-size:13px;color:#111}
.voucher-aggregate-list{position:absolute;left:0;bottom:calc(100% + 14px);background:#fff;border-radius:10px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 10px 30px rgba(0,0,0,0.12);padding:6px;min-width:260px;max-width:360px;max-height:320px;overflow:auto;display:none;z-index:1070}
.voucher-aggregate-list.show{display:block}
.voucher-aggregate-list .item{display:flex;align-items:center;gap:8px;padding:8px;border-radius:8px}
.voucher-aggregate-list .item + .item{border-top:1px solid rgba(0,0,0,0.04)}
.voucher-aggregate-list .item .details{flex:1;min-width:0}
.voucher-aggregate-list .item .title{font-weight:600;font-size:13px;color:#111}
.voucher-aggregate-list .item .code{font-family:monospace;font-size:13px;background:#f8f9fa;padding:4px 6px;border-radius:6px;margin-top:6px;display:inline-block}
.voucher-aggregate-list .item .time{font-size:11px;color:#777;margin-top:6px}
.voucher-aggregate-list .item .actions{display:flex;gap:6px;align-items:center}
.voucher-aggregate .agg-actions{margin-left:auto;display:flex;gap:8px;align-items:center}
@media (max-width:576px){ #voucher-floating-container{left:12px;bottom:12px } .voucher-floating{max-width:calc(100% - 24px);padding:8px} }
`;
        document.head.appendChild(style);
    }

    // Aggregate vouchers into a single expandable toast (store + renderer)
    window.voucherStore = window.voucherStore || [];
    let _aggregateHideTimer = null;

    function parseVoucherNode(node){
        const title = node.querySelector('.voucher-title')?.textContent?.trim() || '';
        const msg = node.querySelector('.voucher-msg')?.getAttribute('title') || node.querySelector('.voucher-msg')?.textContent?.trim() || '';
        const time = node.querySelector('.voucher-time')?.textContent?.trim() || '';
        let code = '';
        const copyBtn = node.querySelector('[onclick^="copyToClipboard"]');
        if(copyBtn){
            const onclick = copyBtn.getAttribute('onclick')||'';
            const m = onclick.match(/copyToClipboard\([^,]+,\s*'([^']+)'\)/);
            if(m) code = m[1];
        }
        return {title,msg,code,time};
    }

    function ensureAggregate(container){
        let agg = container.querySelector('.voucher-aggregate');
        if(agg) return agg;
        agg = document.createElement('div');
        agg.className = 'voucher-aggregate';
        agg.innerHTML = '<div class="toast-icon" aria-hidden="true"><i class="bi bi-gift-fill"></i></div><div class="voucher-body"><div class="agg-title">Thông báo ưu đãi</div></div><div class="agg-actions"><div class="agg-badge">0</div><button class="btn-close ms-1 agg-close" aria-label="Đóng"></button></div>';
        // expanded list
        const list = document.createElement('div');
        list.className = 'voucher-aggregate-list';
        agg.appendChild(list);
        container.appendChild(agg);
        // click toggles list
        agg.addEventListener('click', function(e){
            // prevent toggling when clicking inside copy or close buttons
            if(e.target.closest('.copy-code-btn') || e.target.closest('.agg-close')) return;
            list.classList.toggle('show');
            // pause hide while open
            if(list.classList.contains('show')){ clearTimeout(_aggregateHideTimer); agg.classList.add('show'); }
            else { scheduleAggregateHide(); }
        });
        // close button
        agg.querySelector('.agg-close').addEventListener('click', function(ev){
            ev.stopPropagation();
            agg.classList.remove('show');
            list.classList.remove('show');
        });
        return agg;
    }

    function renderAggregate(container){
        const agg = ensureAggregate(container);
        const list = agg.querySelector('.voucher-aggregate-list');
        const badge = agg.querySelector('.agg-badge');
        badge.textContent = window.voucherStore.length;
        const title = agg.querySelector('.agg-title');
        if(window.voucherStore.length === 0){
            agg.classList.remove('show');
            list.classList.remove('show');
            badge.textContent = '0';
            return;
        }
        title.textContent = window.voucherStore.length === 1 ? window.voucherStore[0].title || 'Ưu đãi mới' : 'Bạn có ' + window.voucherStore.length + ' ưu đãi';
        // build list
        list.innerHTML = '';
        window.voucherStore.slice().reverse().forEach((v, revIdx) => {
            const idx = window.voucherStore.length - 1 - revIdx;
            const item = document.createElement('div');
            item.className = 'item';
            item.innerHTML = `<div class="details"><div class="title">${v.title || ''}</div><div class="code">${v.code || ''}</div><div class="time">${v.time || ''}</div></div><div class="actions"><button class="btn btn-sm copy-code-btn" data-idx="${idx}"><i class="bi bi-clipboard"></i></button><button class="btn btn-sm btn-outline-secondary remove-voucher" data-idx="${idx}"><i class="bi bi-x-lg"></i></button></div>`;
            list.appendChild(item);
        });
        // attach handlers
        list.querySelectorAll('.copy-code-btn').forEach(btn=>{
            btn.addEventListener('click', function(ev){
                ev.stopPropagation();
                const v = window.voucherStore[+this.dataset.idx];
                copyToClipboard(this, v.code);
            });
        });
        list.querySelectorAll('.remove-voucher').forEach(btn=>{
            btn.addEventListener('click', function(ev){
                ev.stopPropagation();
                const idx = +this.dataset.idx;
                window.voucherStore.splice(idx,1);
                renderAggregate(container);
            });
        });
        // show agg
        agg.classList.add('show');
        scheduleAggregateHide();
    }

    function scheduleAggregateHide(delay = 20000){
        clearTimeout(_aggregateHideTimer);
        _aggregateHideTimer = setTimeout(()=>{
            const container = document.getElementById('voucher-floating-container');
            const agg = container && container.querySelector('.voucher-aggregate');
            if(agg){
                agg.classList.remove('show');
                agg.querySelector('.voucher-aggregate-list')?.classList.remove('show');
            }
        }, delay);
    }

    // process existing vouchers: collect data into store, remove nodes
    document.querySelectorAll('.voucher-notification').forEach(el=>{
        try{
            const container = document.getElementById('voucher-floating-container');
            if(container){
                const data = parseVoucherNode(el);
                window.voucherStore.push(data);
                el.remove();
            }
        }catch(e){ /* ignore */ }
    });
    // finally render aggregate if any
    const container = document.getElementById('voucher-floating-container');
    if(container && window.voucherStore.length) renderAggregate(container);


    // Quan sát các node mới để áp dụng tự ẩn và khởi tạo ticker cho thông báo voucher động
    const voucherObserver = new MutationObserver(mutations => {
        mutations.forEach(m => {
            m.addedNodes.forEach(node => {
                if(node.nodeType !== 1) return;
                const container = document.getElementById('voucher-floating-container');
                // nếu node chính là một voucher, di chuyển vào container, bật animation, lên lịch ẩn
                if(node.classList && node.classList.contains('voucher-notification')) {
                    try{
                        if(container){
                            const data = parseVoucherNode(node);
                            window.voucherStore.push(data);
                            node.remove();
                            renderAggregate(container);
                        }
                    }catch(e){ /* ignore */ }
                }
                // nếu node chứa voucher con, xử lý từng cái
                if(node.querySelectorAll) {
                    node.querySelectorAll('.voucher-notification').forEach(el=>{
                        try{
                            if(container){
                                const data = parseVoucherNode(el);
                                window.voucherStore.push(data);
                                el.remove();
                                renderAggregate(container);
                            }
                        }catch(e){ /* ignore */ }
                    });
                }
            });
        });
    });
    voucherObserver.observe(document.body, { childList: true, subtree: true });

    function updateCartBadge(count){
        const badge = document.getElementById('cartBadge');
        if(badge){
            badge.textContent = count;
            badge.style.transition='transform 0.2s';
            badge.style.transform='scale(1.2)';
            setTimeout(()=>{badge.style.transform='scale(1)'},200);
        }
    }

});
</script>
<script>
function copyToClipboard(btn, code) {
    if(typeof btn === 'string' && typeof code === 'undefined'){
        // backward compat: if called as copyToClipboard(code)
        code = btn; btn = null;
    }
    if(!navigator.clipboard){
        alert('Trình duyệt không hỗ trợ sao chép tự động');
        return;
    }
    navigator.clipboard.writeText(code).then(function() {
        // inline feedback: toggle .copied class and swap icon briefly
        try{
            if(btn){
                btn.classList.add('copied');
                const icon = btn.querySelector('i');
                if(icon){ icon.classList.remove('bi-clipboard'); icon.classList.add('bi-check-lg'); }
                setTimeout(()=>{
                    if(btn){
                        btn.classList.remove('copied');
                        if(icon){ icon.classList.remove('bi-check-lg'); icon.classList.add('bi-clipboard'); }
                    }
                }, 1400);
            } else {
                alert('Đã sao chép mã: ' + code);
            }
        }catch(e){ /* ignore */ }
    }, function(err) {
        alert('Không thể sao chép: ' + err);
    });
}
</script>

@endsection
