<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Aplikasi Pemilu' }}</title>
    @vite('resources/css/app.css')
</head>
<body class ="bg-gray-100 text-gray-900">
    <x-navbar>
        <main class = "container mx-auto px-4 py-6">
            {{ $slot }}
        </main>
    </x-navbar>
    
</body>
</html>