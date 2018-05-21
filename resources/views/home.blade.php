<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css" integrity="sha256-LpykTdjMm+jVLpDWiYOkH8bYiithb4gajMYnIngj128=" crossorigin="anonymous">
    <style>
      header {
        padding-left: 30px;
        padding-right: 30px;
      }

      .homepage {
        color: inherit !important;
        text-decoration: none !important;
      }

      .description {
        max-width: 20vw;
      }
    </style>
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
              <td class="align-middle">{{ number_format($package->downloads) }}</td>
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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js" integrity="sha256-qcV1wr+bn4NoBtxYqghmy1WIBvxeoe8vQlCowLG+cng=" crossorigin="anonymous" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js" integrity="sha256-PahDJkda1lmviWgqffy4CcrECIFPJCWoa9EAqVx7Tf8=" crossorigin="anonymous" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/URI.min.js" integrity="sha256-D3tK9Rf/fVqBf6YDM8Q9NCNf/6+F2NOKnYSXHcl0keU=" crossorigin="anonymous" defer></script>
    <script>
      $(document).ready(function() {
        $('#hide-official-packages').change(function () {
          let url = URI(location.href);
          let query = url.search(true);

          query.hide_official_packages = $(this).is(":checked") ? '1' : '0';

          url.search(query);

          location.href = url.toString();
        });

        let table = $('#top-packages').DataTable({
          'pageLength': 100,
          'order': [[1, 'desc'], [2, 'desc']],
          'columns': [
            { 'searchable': false, 'orderable': false },
            { 'searchable': false },
            { 'searchable': false },
            null,
            { 'orderable': false },
            null,
            null
          ]
        });

        table.on('order.dt search.dt', function () {
          table.column(0, { search:'applied', order:'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
          });
        }).draw();
      });
    </script>
  </body>
</html>
