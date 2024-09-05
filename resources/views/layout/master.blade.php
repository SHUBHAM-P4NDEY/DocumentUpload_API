<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="topnav">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <title>Document Upload</title>
</head>

<body>

    <div class="wrapper">

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    @yield('content')

                </div>
            </div>
        </div>

    </div>

    @yield('scripts')
</body>

</html>
