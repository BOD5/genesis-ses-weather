<x-mail::message>
    # Оновлення погоди для міста: {{ $city }}

    Доброго дня!

    Поточна погода у місті **{{ $city }}**:

    - **Температура:** {{ $temperature }}°C
    - **Вологість:** {{ $humidity }}%
    - **Опис:** {{ $description }}

    @if ($icon)
        <img src="http:{{ $icon }}" alt="Weather icon">
    @endif

    Дякуємо, що користуєтесь нашим сервісом!

    З повагою,<br>
    {{ $appName }}

    <x-slot:subcopy>
        Якщо ви більше не хочете отримувати ці оновлення, ви можете <x-mail::button :url="$unsubscribeUrl"
            color="error">відписатися</x-mail::button>.
    </x-slot:subcopy>
</x-mail::message>
