<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Підтвердження підписки</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            background-color: #f4f4f4;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        td {
            border-collapse: collapse;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            margin: 0 0 1em 0;
        }

        a {
            color: #007bff;
            text-decoration: underline;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .content {
            padding: 20px;
            text-align: left;
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
        }

        .header {
            padding: 20px;
            text-align: center;
            background-color: #eeeeee;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
        }

        .button-container {
            text-align: center;
            margin: 20px 0;
        }

        .button {
            background-color: #007bff;
            color: #ffffff !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>

<body>
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0"
        style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" class="container" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="header">
                            <h1 style="margin:0; font-family: Arial, sans-serif; font-size: 24px; color: #333333;">
                                {{ $appName }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <h2 style="font-family: Arial, sans-serif; font-size: 20px; color: #333333; margin-top:0;">
                                Підтвердження підписки</h2>
                            <p>Дякуємо за підписку на оновлення погоди для міста <strong>{{ $city }}</strong> на
                                адресу <strong>{{ $subscriberEmail }}</strong>!</p>
                            <p>Будь ласка, натисніть кнопку нижче, щоб підтвердити вашу підписку:</p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                class="button-container" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $confirmationUrl }}" class="button"
                                            style="background-color: #007bff; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Підтвердити
                                            підписку</a>
                                    </td>
                                </tr>
                            </table>
                            <p>Якщо у вас виникають проблеми з натисканням кнопки "Підтвердити підписку", скопіюйте та
                                вставте URL-адресу нижче у свій веб-браузер:</p>
                            <p><a href="{{ $confirmationUrl }}"
                                    style="color: #007bff; text-decoration: underline; word-break: break-all;">{{ $confirmationUrl }}</a>
                            </p>
                            <p>Якщо ви не запитували цю підписку, будь ласка, проігноруйте цей лист.</p>
                            <p>Дякуємо,<br>{{ $appName }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
