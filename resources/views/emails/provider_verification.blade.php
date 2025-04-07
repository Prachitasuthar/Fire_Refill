<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body>
    <h2>Welcome, {{ $user->first_name }}!</h2>
    <p>Click the link below to verify your email and activate your provider account:</p>
    <a href="{{ url('/provider/verify/' . $user->id) }}">Verify Email</a>
</body>
</html>
