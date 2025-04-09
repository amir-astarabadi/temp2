
<!DOCTYPE html>
<html>
<head>
    <title>Forget Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; text-align: center;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; display: inline-block; max-width: 500px; margin: auto;">
        <h1 style="color: #2c3e50;">Forget Password</h1>
        <p>If you requested for this email click on link below (otherwise ignore it):</p>
        <p>
            <a href="{{ $passwordForgetUrl }}"
               style="display: inline-block; padding: 10px 20px; background-color: #3498db; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </p>
        <p>If you did not request this, no further action is required.</p>
        <p style="margin-top: 30px;">Regards,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
