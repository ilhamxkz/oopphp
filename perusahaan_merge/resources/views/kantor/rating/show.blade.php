@extends('layouts.kantor')

@section('title','Detail Rating')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-star me-2"></i>Detail Rating</h4>
    <p><strong>ID Rating:</strong> {{ $detailRating->id_rating }}</p>
    <p><strong>Rating:</strong> {{ $detailRating->rating }}</p>
    <p><strong>Persentase Bonus:</strong> {{ number_format($detailRating->persentase_bonus, 2) }}%</p>
    <a href="{{ route('rating.index') }}" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
@endsection
