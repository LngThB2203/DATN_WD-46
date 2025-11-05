<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ mới</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
            Liên hệ mới từ website
        </h2>
        
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Họ tên:</strong> {{ $contact->name }}</p>
            <p><strong>Email:</strong> {{ $contact->email }}</p>
            @if($contact->phone)
            <p><strong>Điện thoại:</strong> {{ $contact->phone }}</p>
            @endif
            @if($contact->subject)
            <p><strong>Chủ đề:</strong> {{ $contact->subject }}</p>
            @endif
            <p><strong>Thời gian:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div style="background-color: #ffffff; padding: 20px; border-left: 4px solid #3498db; margin: 20px 0;">
            <h3 style="color: #2c3e50; margin-top: 0;">Nội dung:</h3>
            <p style="white-space: pre-wrap;">{{ $contact->message }}</p>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #7f8c8d; font-size: 12px;">
            <p>Email này được gửi tự động từ hệ thống quản lý liên hệ.</p>
        </div>
    </div>
</body>
</html>

