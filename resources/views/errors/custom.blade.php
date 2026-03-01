<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification' }} - PrimeLand Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; text-align: center; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 400px; width: 100%; }
        .icon { font-size: 60px; color: #e77a3a; margin-bottom: 20px; }
        h1 { color: #333; font-size: 24px; margin-bottom: 10px; }
        p { color: #666; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 25px; background: #e77a3a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; transition: background 0.3s; }
        .btn:hover { background: #d66929; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <i class="fa fa-info-circle"></i>
        </div>
        <h1>{{ $title ?? 'Attention' }}</h1>
        <p>{{ $message ?? 'Something went wrong.' }}</p>
        <a href="/" class="btn">Back to Website</a>
    </div>
</body>
</html>
