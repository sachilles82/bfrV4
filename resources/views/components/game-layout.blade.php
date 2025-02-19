<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="htmlRoot" class="dark h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hedonix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/css/hedonix.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/uifactory@1.18.0/dist/uifactory.min.js" import="@comic-gen"></script>
    <style>
        canvas {
            border: 1px solid black;
        }
        #question-buttons {
            margin-top: 20px;
        }
        button {
            margin: 5px;
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>
<canvas id="gameCanvas" width="800" height="600"></canvas>
<div id="question-buttons"></div>
<!-- Der Start-Button wird außerhalb des Canvas platziert -->
<button id="start-button">Start Game</button>
<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
