<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Vue App</title>

    @vite('resources/js/app.js') <!-- Loads Vue, Bootstrap, Pinia -->
</head>
<body class="antialiased">
    <div id="app"></div> <!-- Vue app mounts here -->
</body>
</html>