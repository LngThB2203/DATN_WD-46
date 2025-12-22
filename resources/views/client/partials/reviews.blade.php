@foreach($reviews as $review)
    @php
        $reviewerName = $review->user->name ?? 'Người dùng';
        $initial = mb_strtoupper(mb_substr(trim($reviewerName), 0, 1));
        $rating = (int) ($review->rating ?? 0);
    @endphp
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 42px; height: 42px; background: rgba(13,110,253,0.08); color: #0d6efd; font-weight: 700;">
                        {{ $initial }}
                    </div>

                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div class="fw-semibold">{{ $reviewerName }}</div>
                            <div class="d-inline-flex align-items-center gap-1" aria-label="{{ $rating }} sao">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $rating >= $i ? 'bi-star-fill' : 'bi-star' }}" style="color: {{ $rating >= $i ? '#f4c430' : '#d0d5dd' }};"></i>
                                @endfor
                                <span class="text-muted small ms-1">{{ $rating }}/5</span>
                            </div>
                        </div>

                        @if($review->comment)
                            <div class="mt-2 text-muted">{{ $review->comment }}</div>
                        @else
                            <div class="mt-2 text-muted fst-italic">(Không có nhận xét)</div>
                        @endif
                    </div>
                </div>

                <div class="text-muted small text-end flex-shrink-0">
                    {{ $review->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
@endforeach
