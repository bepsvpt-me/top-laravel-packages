@extends('base')

@section('title', 'Top 1,000 Laravel Packages')

@section('header')
  @php($date = now()->subDays(2))

  <div class="d-flex justify-content-md-end mt-3 mt-md-0">
    <span>{{ __('base.ranking') }}：</span>

    <a href="{{ route('ranking', ['type' => 'daily', 'date' => $date->toDateString()]) }}">
      {{ __('base.daily') }}
    </a>

    @include('components.dot')

    <a href="{{ route('ranking', ['type' => 'weekly', 'date' => $date->startOfWeek()->toDateString()]) }}">
      {{ __('base.weekly') }}
    </a>

    @include('components.dot')

    <a href="{{ route('ranking', ['type' => 'monthly', 'date' => $date->format('Y-m')]) }}">
      {{ __('base.monthly') }}
    </a>

    @include('components.dot')

    <a href="{{ route('ranking', ['type' => 'yearly', 'date' => $date->year]) }}">
      {{ __('base.yearly') }}
    </a>
  </div>
@endsection

@section('main')
  <table class="table table-striped table-bordered">
    <thead class="thead-light">
      <tr>
        <th class="sticky-top text-center" rowspan="2">#</th>

        <th class="sticky-top text-center" rowspan="2">
          {{ __('base.downloads') }}
        </th>

        <th class="sticky-top text-center" rowspan="2">
          {{ __('base.favorites') }}
        </th>

        <th class="sticky-top" rowspan="2">
          {{ __('base.name') }}
        </th>

        <th class="sticky-top" rowspan="2">
          {{ __('base.description') }}
        </th>

        <th class="sticky-top pb-0 text-center" colspan="2">
          {{ __('base.minimum_requirement') }}
        </th>
      </tr>

      <tr>
        <th class="sticky-top pt-0 text-center home-sticky-top-fixer">
          PHP
        </th>

        <th class="sticky-top pt-0 text-center home-sticky-top-fixer">
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
