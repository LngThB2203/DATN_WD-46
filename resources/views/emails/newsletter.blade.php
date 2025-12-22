<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .header {
            background-color: #ce8460;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: white;
            padding: 20px;
            line-height: 1.8;
        }

        .footer {
            background-color: #f0f0f0;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
        }

        a {
            color: #ce8460;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>46 Perfume</h1>
        </div>

        <div class="content">
            <h2>{{ $subject }}</h2>
            <div>
                {!! nl2br(e($content)) !!}
            </div>

            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ 46 Perfume</strong>
            </p>
        </div>

        <div class="footer">
            <p>© 2025 46 Perfume. All rights reserved.</p>
            <p>Nếu bạn không muốn nhận email từ chúng tôi, vui lòng bỏ theo dõi.</p>
        </div>
    </div>
</body>

</html>
