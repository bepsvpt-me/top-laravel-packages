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
    <thead class="thead-light text-center">
      <tr>
        <th rowspan="2">#</th>

        <th rowspan="2">
          {{ __('base.downloads') }}
        </th>

        <th rowspan="2">
          {{ __('base.favorites') }}
        </th>

        <th class="text-left" rowspan="2">
          {{ __('base.name') }}
        </th>

        <th class="text-left" rowspan="2">
          {{ __('base.description') }}
        </th>

        <th colspan="2">
          {{ __('base.minimum_requirement') }}
        </th>
      </tr>

      <tr>
        <th>PHP</th>

        <th>Laravel</th>
      </tr>
    </thead>

    <tbody>
      @forelse ($packages as $package)
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
      @empty
        <tr>
          <td class="text-center" colspan="7">{{ __('base.empty_result') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
