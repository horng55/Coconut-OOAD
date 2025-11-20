<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $ogTitle = config('app.name', 'School Management System');
        $ogDescription = 'Comprehensive school management system for teachers, students, and parents. Manage enrollments, classes, attendance, grading, announcements, and messaging all in one place.';
        $ogImage = asset('cth_logo.png');
        $ogUrl = url()->current();
    @endphp

    <title>{{ $ogTitle }}</title>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <meta name="description"
          content="{{ $ogDescription }}">
    <meta name="keywords"
          content="school management system, education management, student management, teacher portal, parent portal, attendance tracking, grade management, class enrollment, school administration">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="{{ config('app.name', 'School Management System') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @routes
    @vite([
        'resources/css/app.css',
        'resources/sass/app.scss',
    ])
    @inertiaHead
</head>

<body class="font-sans antialiased scrollbar-thin scrollbar-thumb-purple-600 scrollbar-track-purple-300">
@inertia
@vite('resources/js/app.js')

</body>
</html>
