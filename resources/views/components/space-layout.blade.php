<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    id="htmlRoot"
    class="dark h-full overflow-hidden"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Space Invaders</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Bungee&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/space.css', 'resources/js/app.js'])


</head>

<body>
<canvas id="canvas1"></canvas>

<div class="controls">
    <button id="fullScreenButton">F</button>
    <button id="resetButton">R</button>r
</div>

<div>
    <img src="{{ asset('images/space/crew.png') }}" alt="crew" id="crew">
</div>

<script src="{{ asset('js/space/enemy.js') }}"></script>
<script src="{{ asset('js/space/main.js') }}"></script>

</body>
</html>
