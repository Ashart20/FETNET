<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - FETNet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- atau sesuai setup Tailwind --}}
    @livewireStyles
</head>
<body class="min-h-screen w-full bg-gray-100 antialiased flex items-center justify-center">
{{ $slot }}

@livewireScripts
</body>

</html>
