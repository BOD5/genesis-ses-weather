<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Підписка на оновлення погоди</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="text"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #4cae4c;
        }

        .message {
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
            text-align: center;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Підписка на оновлення погоди</h1>

        <form id="subscriptionForm">
            @csrf <div>
                <label for="email">Ваш Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="city">Місто (англійською, наприклад, Kyiv):</label>
                <input type="text" id="city" name="city" required>
            </div>

            <div>
                <label for="frequency">Частота оновлень:</label>
                <select id="frequency" name="frequency" required>
                    <option value="daily">Щоденно</option>
                    <option value="hourly">Щогодини</option>
                </select>
            </div>

            <button type="submit">Підписатися</button>
        </form>

        <div id="formMessage" class="message" style="display: none;"></div>

        <hr style="margin: 30px 0;">

        <h2>Дізнатися поточну погоду</h2>
        <form id="getWeatherForm">
            <div>
                <label for="weatherCity">Місто (англійською):</label>
                <input type="text" id="weatherCity" name="weatherCity" required>
            </div>
            <button type="submit">Отримати погоду</button>
        </form>
        <div id="weatherResult" class="message" style="display: none;"></div>

    </div>

    <script>
        document.getElementById('subscriptionForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const messageDiv = document.getElementById('formMessage');

            fetch('/api/subscribe', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json().then(data => ({
                    status: response.status,
                    body: data
                })))
                .then(data => {
                    messageDiv.style.display = 'block';
                    messageDiv.textContent = data.body.message || data.body.error || (data.body.errors ? JSON.stringify(data.body.errors) : 'Сталася помилка.');
                    if (data.status === 200 || data.status === 201) {
                        messageDiv.className = 'message success';
                        form.reset();
                    } else {
                        messageDiv.className = 'message error';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.style.display = 'block';
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'Не вдалося відправити запит. Перевірте консоль.';
                });
        });
        document.getElementById('getWeatherForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const city = document.getElementById('weatherCity').value;
            const weatherResultDiv = document.getElementById('weatherResult');

            if (!city) {
                weatherResultDiv.textContent = 'Будь ласка, введіть назву міста.';
                weatherResultDiv.className = 'message error';
                weatherResultDiv.style.display = 'block';
                return;
            }

            fetch(`/api/weather?city=${encodeURIComponent(city)}`)
                .then(response => response.json().then(data => ({
                    status: response.status,
                    body: data
                })))
                .then(data => {
                    weatherResultDiv.style.display = 'block';
                    if (data.status === 200) {
                        weatherResultDiv.className = 'message success';
                        weatherResultDiv.innerHTML = `
                            <strong>${decodeURIComponent(city)}:</strong><br>
                            Температура: ${data.body.temperature}°C<br>
                            Вологість: ${data.body.humidity}%<br>
                            Опис: ${data.body.description}
                        `;
                    } else {
                        weatherResultDiv.className = 'message error';
                        weatherResultDiv.textContent = data.body.message || data.body.error || (data.body.errors ? JSON.stringify(data.body.errors) : 'Помилка отримання погоди.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    weatherResultDiv.style.display = 'block';
                    weatherResultDiv.className = 'message error';
                    weatherResultDiv.textContent = 'Не вдалося отримати погоду. Перевірте консоль.';
                });
        });
    </script>
</body>

</html>