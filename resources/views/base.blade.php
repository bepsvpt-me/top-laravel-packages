<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="List top 1,000 downloads Laravel packages.">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <title>@yield('title') | Laravel Packages</title>
  </head>
  <body class="d-flex flex-column">
    <header class="m-3">
      <div class="row align-items-end">
        <div class="col">
          <h1 class="m-0">@yield('title')</h1>
        </div>

        <div class="col-12 col-md-5">
          @yield('header')
        </div>
      </div>
    </header>

    <main class="flex-grow-1 mx-3">
      <section class="wrapper">
        @yield('main')
      </section>
    </main>

    <footer class="footer py-3">
      <div class="container-fluid text-center text-muted">
        <p class="mb-1">
          @component('components.external-link')
            @slot('href', 'https://github.com/bepsvpt-me/laravel.bepsvpt.me')

            <span>GitHub</span>
          @endcomponent
        </p>

        <span>bepsvpt.me © {{ date('Y') }}</span>
      </div>
    </footer>
  </body>
</html>
