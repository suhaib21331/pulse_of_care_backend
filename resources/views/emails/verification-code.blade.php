<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .code { font-size: 42px; font-weight: bold; letter-spacing: 10px; color: #2d6cdf; text-align: center; margin: 32px 0; }
        .footer { font-size: 12px; color: #999; margin-top: 32px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello, {{ $user->full_name }}</h2>
        <p>Use the code below to verify your email address. It expires in <strong>10 minutes</strong>.</p>
        <div class="code">{{ $code }}</div>
        <p>If you did not create an account, you can safely ignore this email.</p>
        <div class="footer">{{ config('app.name') }}</div>
    </div>
</body>
</html>
