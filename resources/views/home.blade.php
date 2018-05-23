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
              Top Laravel Packages
            </a>
          </h1>
        </div>

        <div class="col align-self-end text-right">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="hide-official-packages" {{ $hideOfficialPackages ? 'checked' : '' }}>

            <label class="form-check-label" for="defaultCheck1">
              Hide Laravel Official Packages
            </label>
          </div>
        </div>
      </div>

      <hr>
    </header>

    <main class="container-fluid">
      <table id="top-packages" class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Downloads</th>
            <th>Favorites</th>
            <th>Name</th>
            <th>Description</th>
            <th>Minimum PHP Version</th>
            <th>Minimum Laravel Version</th>
          </tr>
        </thead>

        <tbody>
          @foreach($packages as $package)
            <tr>
              <td class="text-center align-middle">{{ $loop->iteration }}</td>
              <td class="align-middle">{{ number_format($package->getAttribute('downloads')) }}</td>
              <td class="align-middle">{{ number_format($package->favers) }}</td>
              <td class="align-middle">
                <a href="{{ $package->url }}" target="_blank" rel="noopener">
                  {{ $package->name }}
                </a>
              </td>
              <td class="description">{{ $package->description ?: '-' }}</td>
              <td class="text-center align-middle">{{ $package->min_php_version ?: '-' }}</td>
              <td class="text-center align-middle">{{ $package->min_laravel_version ?: '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </main>

    <footer class="footer my-3">
      <div class="container-fluid text-center text-muted">
        <span>Copyright © {{ date('Y') }}</span>
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
