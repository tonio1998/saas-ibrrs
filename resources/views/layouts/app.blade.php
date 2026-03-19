<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('styles')
</head>
<body>

@include('components.navbar')

<div class="container-fluid">
    <div class="row">

        @include('components.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @yield('content')
        </main>

    </div>
</div>

<x-alerts />

@include('components.footer')

@stack('scripts')

</body>
</html>
