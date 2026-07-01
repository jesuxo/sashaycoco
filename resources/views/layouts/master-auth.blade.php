<!doctype html>
<html lang="en" data-bs-theme="light" data-footer="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="SISDATO - Sistema dado para todos" name="description">
    <meta content="Themesbrand" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>https://www.grupoabdul.com - SISDATO</title>
    <meta name="description" content="Grupo Abdul">
    <link rel="canonical" href="https://www.grupoabdul.com">
    <meta property="og:title" content="https://www.grupoabdul.com - SISDATO">
    <meta property="og:description" content=" Grupo Abdul">
    <meta property="og:type" content="WebPage">
    <meta property="og:image" content="https://grupoabdul.com/build/images/logo.png">
    <meta property="og:url" content="https://grupoabdul.com">

    <meta name="twitter:title" content="https://www.grupoabdul.com - SISDATO ">
    <meta name="twitter:description" content=" Grupo Abdul ">
    <meta name="twitter:site" content="@grupoabdul">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebPage","name":"Grupo Abdul","description":" "}</script>
    <script
        src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
        crossorigin="anonymous"></script>
    <!-- head css -->
    @include('layouts.head-css')
</head>

<body>

    <section
        class="auth-page-wrapper position-relative bg-light min-vh-100 d-flex align-items-center justify-content-between">


        <!--content here-->
        @yield('content')
    </section>

    <!--script-->
    @include('layouts.vendor-scripts')
</body>

</html>
