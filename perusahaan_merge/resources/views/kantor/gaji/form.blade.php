@extends('layouts.kantor')

@section('title', ($editing? 'Edit':'Tambah').' Gaji')

@section('content')
<div class="card">
  <div class="card-body">
    <h4><i class="fas fa-money-bill-wave me-2"></i>{{ $editing ? 'Edit' : 'Hitung & Tambah' }} Gaji</h4>
    <form method="post" action="{{ $editing ? route('gaji.update',$editing->id_gaji) : route('gaji.store') }}">
      @csrf
      @if($editing) @method('PUT') @endif
      <div class="row">
        <div class="col-md-4 mb-2">
          <label>Nama Karyawan</label>
          <select name="id_karyawan" required class="form-select">
            <option value="">-- Pilih --</option>
            @foreach($karyawans as $k)
              <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan', $editing->id_karyawan ?? '')==$k->id_karyawan ? 'selected' : '' }}>
                {{ $k->nama }} - {{ optional($k->jabatan)->jabatan }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label>Tarif Lembur</label>
          <select name="id_lembur" class="form-select">
            <option value="">-- Pilih --</option>
            @foreach($lemburs as $l)
              <option value="{{ $l->id_lembur }}" {{ old('id_lembur', $editing->id_lembur ?? '')==$l->id_lembur ? 'selected' : '' }}>
                Rp {{ number_format($l->tarif,0,',','.') }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label>Periode</label>
          <input name="periode" class="form-control" value="{{ old('periode', $editing->periode ?? date('Y-m')) }}">
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-4 mb-2">
          <label>Lama Lembur (jam)</label>
          <input name="lama_lembur" class="form-control" value="{{ old('lama_lembur', $editing->lama_lembur ?? 0) }}">
        </div>
      </div>
      <button class="btn btn-primary mt-2">
        <i class="fas fa-calculator me-1"></i>{{ $editing ? 'Update' : 'Hitung & Simpan' }}
      </button>
      <a href="{{ route('gaji.index') }}" class="btn btn-secondary mt-2">Batal</a>
    </form>
  </div>
</div>
@endsection
