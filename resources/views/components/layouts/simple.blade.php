<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FETNet - Jadwal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen flex items-center justify-center">
<main class="p-4">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>

