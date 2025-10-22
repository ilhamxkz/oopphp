@extends('layouts.kantor')

@section('title','Detail Gaji')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-wallet me-2"></i>Detail Gaji</h4>
    <p><strong>ID Gaji:</strong> {{ $detailGaji->id_gaji }}</p>
    <p><strong>Nama Karyawan:</strong> {{ optional($detailGaji->karyawan)->nama }}</p>
    <p><strong>Gaji Pokok:</strong> Rp {{ number_format(optional(optional($detailGaji->karyawan)->jabatan)->gaji_pokok ?? 0, 0, ',', '.') }}</p>
    <p><strong>ID Lembur:</strong> {{ $detailGaji->id_lembur }}</p>
    <p><strong>Periode:</strong> {{ $detailGaji->periode }}</p>
    <p><strong>Lama Lembur:</strong> {{ $detailGaji->lama_lembur }} jam</p>
    <p><strong>Total Lembur:</strong> Rp {{ number_format($detailGaji->total_lembur,0,',','.') }}</p>
    <p><strong>Total Bonus:</strong> Rp {{ number_format($detailGaji->total_bonus,0,',','.') }}</p>
    <p><strong>Total Tunjangan:</strong> Rp {{ number_format($detailGaji->total_tunjangan,0,',','.') }}</p>
    <p><strong>Total Pendapatan:</strong> Rp {{ number_format($detailGaji->total_pendapatan,0,',','.') }}</p>
    <a href="{{ route('gaji.index') }}" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>
@endsection
