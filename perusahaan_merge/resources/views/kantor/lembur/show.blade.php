@extends('layouts.kantor')

@section('title','Detail Lembur')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-clock me-2"></i>Detail Lembur</h4>
    <p><strong>ID Lembur:</strong> {{ $detailLembur->id_lembur }}</p>
    <p><strong>Tarif:</strong> Rp {{ number_format($detailLembur->tarif,0,',','.') }}</p>
    <a href="{{ route('lembur.index') }}" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
@endsection
