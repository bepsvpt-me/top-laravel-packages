@extends('base')

@section('title', 'Top 1,000 Laravel Packages')

@section('header')
  @php($date = now()->subDays(2))

  <div class="text-md-right mt-3 mt-md-0">
    <span>Ranking：</span>
    <a href="{{ route('ranking', ['type' => 'daily', 'date' => $date->toDateString()]) }}">Daily</a>
    @include('components.dot')
    <a href="{{ route('ranking', ['type' => 'weekly', 'date' => $date->startOfWeek()->toDateString()]) }}">Weekly</a>
    @include('components.dot')
    <a href="{{ route('ranking', ['type' => 'monthly', 'date' => $date->format('Y-m')]) }}">Monthly</a>
    @include('components.dot')
    <a href="{{ route('ranking', ['type' => 'yearly', 'date' => $date->year]) }}">Yearly</a>
  </div>
@endsection

@section('main')
  <table class="table table-striped table-bordered">
    <thead class="thead-light">
      <tr>
        <th class="sticky-top text-center" rowspan="2">#</th>
        <th class="sticky-top text-center" rowspan="2">Downloads</th>
        <th class="sticky-top text-center" rowspan="2">Favorites</th>
        <th class="sticky-top" rowspan="2">Name</th>
        <th class="sticky-top" rowspan="2">Description</th>
        <th class="sticky-top pb-0 text-center" colspan="2">Minimum Requirement</th>
      </tr>

      <tr>
        <th
          class="sticky-top pt-0 text-center"
          style="top: 2.35rem;"
        >
          PHP
        </th>
        <th
          class="sticky-top pt-0 text-center"
          style="top: 2.35rem;"
        >
          Laravel
        </th>
      </tr>
    </thead>

    <tbody>
      @foreach ($packages as $package)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td class="text-right">{{ number_format($package->getAttribute('downloads')) }}</td>
          <td class="text-right">{{ number_format($package->favers) }}</td>
          <td class="name text-break text-wrap">
            @component('components.external-link')
              @slot('href', $package->url)

              <span>{{ $package->name }}</span>
            @endcomponent
          </td>
          <td class="description text-break text-wrap">{{ $package->description ?: '-' }}</td>
          <td class="text-center">{{ $package->min_php_version ?: '-' }}</td>
          <td class="text-center">{{ $package->min_laravel_version ?: '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
