@extends('base')

@php
  $type = request()->route('type');
  $uType = 'daily' === $type ? 'day' : substr($type, 0, -2);
  $date = Carbon\Carbon::createFromDate(...explode('-', request()->route('date')));
  $last = $date->copy()->{'sub'.ucfirst($uType)}()->{'startOf'.ucfirst($uType)}();
  $next = $date->copy()->{'add'.ucfirst($uType)}()->{'startOf'.ucfirst($uType)}();
@endphp

@section('title')
  {{ __(sprintf('base.%s_ranking', $type)) }} - {{ request()->route('date') }}
@endsection

@section('header')
  <div class="text-md-right mt-2 mt-md-0">
    @if ($last->isAfter('2012-06-01'))
      <a
        href="{{ route('ranking', ['type' => $type, 'date' => $last->format(sprintf('Y%s%s', $type !== 'yearly' ? '-m' : '' , in_array($type, ['daily', 'weekly']) ? '-d' : ''))]) }}"
      >{{ __(sprintf('base.prev_%s', $uType)) }}</a>
    @endif

    @if ($last->isAfter('2012-06-01') && !$next->isFuture())
      @include('components.dot')
    @endif

    @unless ($next->isFuture())
      <a
        href="{{ route('ranking', ['type' => $type, 'date' => $next->format(sprintf('Y%s%s', $type !== 'yearly' ? '-m' : '' , in_array($type, ['daily', 'weekly']) ? '-d' : ''))]) }}"
      >{{ __(sprintf('base.next_%s', $uType)) }}</a>
    @endunless
  </div>
@endsection

@section('main')
  <table class="table table-striped table-bordered">
    <thead class="thead-light text-center">
      <tr>
        <th>#</th>

        <th>
          {{ __('base.downloads') }}
        </th>

        <th class="text-left">
          {{ __('base.name') }}
        </th>

        <th class="text-left">
          {{ __('base.description') }}
        </th>
      </tr>
    </thead>

    <tbody>
      @forelse ($ranks as $rank)
        <tr>
          <td class="text-center">{{ number_format($loop->iteration) }}</td>
          <td class="text-right">{{ number_format($rank->downloads) }}</td>
          <td class="name text-break text-wrap">
            @component('components.external-link')
              @slot('href', $rank->package->url)

              <span>{{ $rank->package->name }}</span>
            @endcomponent
          </td>
          <td class="description text-break text-wrap">{{ $rank->package->description ?: '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center">{{ __('base.empty_result') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
