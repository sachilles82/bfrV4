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

    <title>Hedonix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&display=swap" rel="stylesheet">

    <!-- Fonts -->
{{--    <link rel="preconnect" href="https://fonts.bunny.net">--}}
{{--    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>--}}

    <!-- Scripts -->
    @vite(['resources/css/app.css','resources/css/hedonix.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/uifactory@1.18.0/dist/uifactory.min.js" import="@comic-gen"></script>


</head>

<body>

<canvas id="canvas1"></canvas>

<div class="assets">
    <img id="background" src="{{ asset('images/background2.png') }}" alt="background">
    <img id="player" src="{{ asset('images/player.png') }}" alt="player">
    <img id="enemy" src="{{ asset('images/enemy.png') }}" alt="enemy">
</div>

{{--<div class="assets">--}}
{{--    <img id="player" src="{{ asset('images/player1.png') }}" alt="player">--}}
{{--</div>--}}

<script src="{{ asset('js/background.js') }}"></script>
<script src="{{ asset('js/player.js') }}"></script>
<script src="{{ asset('js/enemy.js') }}"></script>
<script src="{{ asset('js/question.js') }}"></script>
<script src="{{ asset('js/playerspeech.js') }}"></script>
<script src="{{ asset('js/enemyspeech.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

</body>
</html>
