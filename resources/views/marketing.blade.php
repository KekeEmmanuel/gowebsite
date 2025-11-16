<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Go Tanzania Safari Ltd</title>
        <link rel="icon" type="image/png" href="/favicon.ico">
    </head>
    <body class="antialiased bg-white">
        <div id="marketing-app"></div>
        @vite('resources/marketing/main.ts')
    </body>
</html>
