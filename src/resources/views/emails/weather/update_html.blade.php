<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення погоди: {{ $city }}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f0f4f8;
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        td {
            border-collapse: collapse;
        }

        .main-table {
            width: 100%;
            background-color: #f0f4f8;
            padding: 20px 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #4a90e2;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 25px 30px;
            color: #333333;
            line-height: 1.6;
            font-size: 16px;
        }

        .content h2 {
            font-size: 20px;
            color: #2c3e50;
            margin-top: 0;
        }

        .content p {
            margin-bottom: 15px;
        }

        .weather-details {
            margin-top: 20px;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #4a90e2;
        }

        .weather-details strong {
            color: #4a90e2;
        }

        .weather-icon {
            vertical-align: middle;
            margin-left: 10px;
        }

        .footer {
            background-color: #e9ecef;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .footer p {
            margin-bottom: 5px;
        }

        .unsubscribe-link {
            color: #007bff;
            text-decoration: underline;
        }

        .button {
            background-color: #dc3545;
            color: #ffffff !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <table role="presentation" class="main-table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" class="container" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="content">
                            <h2>Оновлення погоди для міста: {{ $city }}</h2>
                            <p>Доброго дня!</p>
                            <p>Поточна погода у місті <strong>{{ $city }}</strong>:</p>
                            <div class="weather-details">
                                <p><strong>Температура:</strong> {{ $temperature }}°C</p>
                                <p><strong>Вологість:</strong> {{ $humidity }}%</p>
                                <p><strong>Опис:</strong> {{ $description }}
                                    @if ($icon)
                                        <img src="http:{{ $icon }}" alt="Іконка погоди" width="32"
                                            height="32" class="weather-icon">
                                    @endif
                                </p>
                            </div>
                            <p>Дякуємо, що користуєтесь нашим сервісом!</p>
                            <p>З повагою,<br>Команда {{ $appName }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>Якщо ви більше не хочете отримувати ці оновлення, ви можете
                                <a href="{{ $unsubscribeUrl }}" class="unsubscribe-link"
                                    style="color: #dc3545;">відписатися</a>.
                            </p>
                            <p style="margin-top: 15px;">&copy; {{ date('Y') }} {{ $appName }}. All rights
                                reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
