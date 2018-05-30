@extends('base')

@section('title')
  {{ ucfirst(request()->route('type')) }} Downloads Ranking - {{ request()->route('date') }}
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
      @forelse($ranks as $rank)
        <tr>
          <td class="text-center align-middle">{{ $loop->iteration }}</td>
          <td>{{ $rank->downloads }}</td>
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
