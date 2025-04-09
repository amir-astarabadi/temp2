<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; text-align: center;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; display: inline-block; max-width: 500px; margin: auto;">
        <h1 style="color: #2c3e50;">Email Verification</h1>
        <p>Hello there,</p>
        <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
        <p>
            <a href="{{ $verificationUrl }}"
               style="display: inline-block; padding: 10px 20px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Verify Email Address
            </a>
        </p>
        <p>If you did not create an account, no further action is required.</p>
        <p style="margin-top: 30px;">Regards,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
