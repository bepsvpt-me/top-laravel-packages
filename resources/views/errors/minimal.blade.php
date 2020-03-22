<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Laravel Packages</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
  </head>
  <body class="d-flex align-items-center justify-content-center">
    <h1 class="mb-5 pr-3 border-right font-weight-normal">
      @yield('code')
    </h1>

    <h2 class="mb-5 pl-3 font-weight-light">
      @yield('message')
    </h2>
  </body>
</html>
