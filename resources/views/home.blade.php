@extends('base')

@section('title', 'Top 1000 Laravel Packages')

@section('header')
  @php($date = now()->subDays(2))
  <div class="text-right">
    <span>Ranking：</span>
    <a href="{{ route('ranking', ['type' => 'daily', 'date' => $date->toDateString()]) }}">Daily</a>
    <span class="mx-1">·</span>
    <a href="{{ route('ranking', ['type' => 'weekly', 'date' => $date->startOfWeek()->toDateString()]) }}">Weekly</a>
    <span class="mx-1">·</span>
    <a href="{{ route('ranking', ['type' => 'monthly', 'date' => $date->format('Y-m')]) }}">Monthly</a>
    <span class="mx-1">·</span>
    <a href="{{ route('ranking', ['type' => 'yearly', 'date' => $date->year]) }}">Yearly</a>
  </div>
@endsection

@section('main')
  <table id="top-packages" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Downloads</th>
        <th>Favorites</th>
        <th>Name</th>
        <th>Description</th>
        <th>Min PHP Version</th>
        <th>Min Laravel Version</th>
      </tr>
    </thead>

    <tbody>
      @foreach($packages as $package)
        <tr>
          <td class="text-center align-middle">{{ $loop->iteration }}</td>
          <td class="align-middle">{{ number_format($package->getAttribute('downloads')) }}</td>
          <td class="align-middle">{{ number_format($package->favers) }}</td>
          <td class="align-middle">
            <a
              href="{{ $package->url }}"
              target="_blank"
              rel="noopener"
            >{{ $package->name }}</a>
          </td>
          <td class="description">{{ $package->description ?: '-' }}</td>
          <td class="text-center align-middle">{{ $package->min_php_version ?: '-' }}</td>
          <td class="text-center align-middle">{{ $package->min_laravel_version ?: '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
