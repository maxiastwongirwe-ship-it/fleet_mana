<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <style>
        body {
            background:#f9fafb;
        }
    </style>
</head>
<body>

<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-4xl p-6">
        {{ $slot }}
    </div>
</div>

</body>
</html>