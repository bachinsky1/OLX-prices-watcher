<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Підписки та ціни OLX.UA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-50 dark:bg-gray-700">
    <div id="app" class="flex flex-col min-h-screen"></div>
    @vite(['resources/js/app.js'])
</body>

</html>
