<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-gray-300">404</h1>
        <p class="text-xl text-gray-600 mt-4">Page not found</p>
        <p class="text-sm text-gray-500 mt-2">The page you're looking for doesn't exist.</p>
        <a href="{{ url('/') }}" class="inline-block mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Go Home</a>
    </div>
</body>
</html>
