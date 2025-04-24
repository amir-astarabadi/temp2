<!DOCTYPE html>
<html>

<head>
    <title>Dataset Status Updated</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; text-align: center;">
    <div
        style="background-color: #ffffff; padding: 30px; border-radius: 10px; display: inline-block; max-width: 500px; margin: auto;">
        <h1 style="color: #2c3e50;">
            dataset "{{ $dataset_name }}"
            in project "{{ $project_name }}"
            moved to state {{ $status }}
        </h1>
        <p style="margin-top: 30px;">Regards,<br>{{ config('app.name') }}</p>
    </div>
</body>

</html>
