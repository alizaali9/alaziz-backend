<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password Notification</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Click the button below to reset your password:</p>
    <a href="{{ url('/reset-password?token=' . $token . '&email=' . urlencode($email)) }}">Reset Password</a>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
