@foreach($reviews as $review)
    <div class="list-group-item">
        <div class="d-flex justify-content-between">
            <div>
                <strong>{{ $review->user->name ?? 'Người dùng' }}</strong>
                <span class="ms-2">{{ $review->rating }}/5</span>
            </div>
            <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
        </div>
        @if($review->comment)
            <div class="mt-2">{{ $review->comment }}</div>
        @endif
    </div>
@endforeach
