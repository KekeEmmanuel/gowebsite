<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Go Tanzania Safari Ltd</title>
        <link rel="icon" type="image/png" href="/favicon.ico">
        @vite('resources/marketing/main.ts')
    </head>
    <body class="antialiased m-0 p-0 overflow-x-hidden">
        <div id="marketing-app" class="m-0 p-0 min-h-screen"></div>
    </body>
</html>
