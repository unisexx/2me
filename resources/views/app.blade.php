<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Default description for SEO' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'default, keywords, for, seo' }}">
    <meta name="author" content="{{ $metaAuthor ?? 'Your Website Author' }}">
    <title>{{ $title ?? 'Default Title' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app"></div> <!-- ที่สำหรับ mount Vue App -->
</body>

</html>
