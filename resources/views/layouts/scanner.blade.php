<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body>

<div class="container-flui">
    <main class="col-md-12 ms-sm-auto">
        @yield('content')
    </main>
</div>

<x-alerts />

@include('components.footer')

@stack('scripts')

</body>
</html>
