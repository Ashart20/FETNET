<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FETNet</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">
<livewire:dashboard />

@livewireScripts
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
