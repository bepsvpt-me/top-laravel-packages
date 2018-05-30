<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Top Laravel Packages</title>
  </head>
  <body>
    <header class="container-fluid">
      <div class="row mt-4">
        <div class="col">
          <h1 class="m-0">
            <a href="{{ route('home') }}" class="homepage">
              @yield('title')
            </a>
          </h1>
        </div>

        @yield('header')
      </div>

      <hr>
    </header>

    <main class="container-fluid">
      @yield('main')
    </main>

    <footer class="footer my-3">
      <div class="container-fluid text-center text-muted">
        <span>Copyright © {{ date('Y') }}</span>

        <span> · </span>

        <span>
          <a href="https://github.com/BePsvPT/top-laravel-packages" target="_blank" rel="noopener">
            GitHub
          </a>
        </span>
      </div>
    </footer>

    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}" defer></script>
    <script src="{{ asset('js/popper.min.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}" defer></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}" defer></script>
    <script src="{{ asset('js/URI.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
  </body>
</html>
