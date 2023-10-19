<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
    <h1>Welcome to Our Website</h1>
    <p>Dear New,</p>
    <p>{{ $body }}</p>
    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
