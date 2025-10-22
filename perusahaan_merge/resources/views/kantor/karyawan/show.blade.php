@extends('layouts.kantor')

@section('title','Detail Karyawan')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-user me-2"></i>Detail Karyawan</h4>
    <p><strong>Nama:</strong> {{ $detailKaryawan->nama }}</p>
    <p><strong>Divisi:</strong> {{ $detailKaryawan->divisi }}</p>
    <p><strong>Jabatan:</strong> {{ optional($detailKaryawan->jabatan)->jabatan ?? '-' }}</p>
    <p><strong>Rating:</strong> {{ optional($detailKaryawan->rating)->rating ?? '-' }}</p>
    <p><strong>Umur:</strong> {{ $detailKaryawan->umur }}</p>
    <p><strong>Jenis Kelamin:</strong> {{ $detailKaryawan->jenis_kelamin }}</p>
    <p><strong>Status:</strong> {{ $detailKaryawan->status }}</p>
    <p><strong>Alamat:</strong> {{ $detailKaryawan->alamat }}</p>
    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
@endsection
