<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статус підписки</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .message {
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 1.1em;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .info {
            background-color: #d9edf7;
            color: #31708f;
            border: 1px solid #bce8f1;
        }

        a {
            color: #337ab7;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        @if (isset($status) && isset($message))
            <div class="message {{ $status }}">
                {{ $message }}
            </div>
        @else
            <div class="message error">
                Сталася невідома помилка.
            </div>
        @endif
        <p><a href="{{ route('subscription.form') }}">Повернутися на сторінку підписки</a></p>
    </div>
</body>

</html>
