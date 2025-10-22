@extends('layouts.kantor')

@section('title','Detail Jabatan')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-briefcase me-2"></i>Detail Jabatan</h4>
    <p><strong>Jabatan:</strong> {{ $detailJabatan->jabatan }}</p>
    <p><strong>Gaji Pokok:</strong> Rp {{ number_format($detailJabatan->gaji_pokok,0,',','.') }}</p>
    <p><strong>Tunjangan:</strong> Rp {{ number_format($detailJabatan->tunjangan,0,',','.') }}</p>
    <a href="{{ route('jabatan.index') }}" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
@endsection
