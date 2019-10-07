<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>@yield('title') | Laravel Packages</title>
  </head>
  <body>
    <header class="container-fluid">
      <div class="row align-items-end mt-4">
        <div class="col">
          <h1 class="m-0">@yield('title')</h1>
        </div>

        <div class="col-12 col-md-4">
          @yield('header')
        </div>
      </div>

      <hr>
    </header>

    <main class="container-fluid">
      @yield('main')
    </main>

    <footer class="footer my-3">
      <div class="container-fluid text-center text-muted">
        <span>Copyright © {{ date('Y') }}</span>

        <span class="mx-1">·</span>

        <a
          href="https://github.com/BePsvPT/top-laravel-packages"
          target="_blank"
          rel="noopener"
        >GitHub</a>
      </div>
    </footer>

    <script src="{{ asset('js/datatables.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
  </body>
</html>
