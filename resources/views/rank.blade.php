@extends('base')

@php
  $type = request()->route('type');
  $uType = 'daily' === $type ? 'day' : substr($type, 0, -2);
  $date = Carbon\Carbon::createFromDate(...explode('-', request()->route('date')));
  $last = $date->copy()->{'sub'.ucfirst($uType)}()->{'startOf'.ucfirst($uType)}();
  $next = $date->copy()->{'add'.ucfirst($uType)}()->{'startOf'.ucfirst($uType)}();
@endphp

@section('title')
  {{ ucfirst($type) }} Ranking - {{ request()->route('date') }}
@endsection

@section('header')
  <div class="text-right">
    @if ($last->isAfter('2012-06-01'))
      <a
        href="{{ route('ranking', ['type' => $type, 'date' => $last->format(sprintf('Y%s%s', $type !== 'yearly' ? '-m' : '' , in_array($type, ['daily', 'weekly']) ? '-d' : ''))]) }}"
      >Prev {{ ucfirst($uType) }}</a>
    @endif

    @unless ($next->isFuture())
      <a
        class="ml-2"
        href="{{ route('ranking', ['type' => $type, 'date' => $next->format(sprintf('Y%s%s', $type !== 'yearly' ? '-m' : '' , in_array($type, ['daily', 'weekly']) ? '-d' : ''))]) }}"
      >Next {{ ucfirst($uType) }}</a>
    @endunless
  </div>
@endsection

@section('main')
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Downloads</th>
        <th>Name</th>
        <th>Description</th>
      </tr>
    </thead>

    <tbody>
      @forelse ($ranks as $rank)
        <tr>
          <td class="text-center align-middle">{{ $loop->iteration }}</td>
          <td class="text-center">{{ number_format($rank->downloads) }}</td>
          <td class="align-middle">
            <a href="{{ $rank->package->url }}" target="_blank" rel="noopener">
              {{ $rank->package->name }}
            </a>
          </td>
          <td class="description">{{ $rank->package->description ?: '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center">It seems nothing is here!</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
