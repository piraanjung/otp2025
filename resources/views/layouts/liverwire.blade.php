<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @livewireStyles
    @stack('style')
</head>
<body>
    <main class="container mt-4">
        @yield('content')
    </main>
    
    @livewireScripts
    @stack('script')
</body>
</html>