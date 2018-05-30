@extends('base')

@section('title', 'Top Laravel Packages')

@section('main')
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
@endsection
