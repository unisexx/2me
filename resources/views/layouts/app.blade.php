<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-xl font-bold">My Website</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto mt-4">
        <div id="app"></div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 mt-10">
        <div class="container mx-auto">
            <p>&copy; 2024 My Website. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
