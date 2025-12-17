@extends('client.layouts.client')

@section('content')
<div class="container mt-4">
    <h4>Thông báo của bạn</h4>

    @forelse ($notifications as $notification)
        <div class="alert alert-info">
            <strong>{{ $notification->data['title'] }}</strong><br>
            {{ $notification->data['message'] }}
            <br>
        </div>
    @empty
        <p>Chưa có thông báo</p>
    @endforelse
</div>
@endsection
