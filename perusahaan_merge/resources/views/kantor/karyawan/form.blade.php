@extends('layouts.kantor')

@section('title', ($editing? 'Edit':'Tambah').' Karyawan')

@section('content')
<div class="card">
  <div class="card-body">
    <h5><i class="fas fa-user-plus me-2"></i>{{ $editing ? 'Edit Karyawan' : 'Tambah Karyawan' }}</h5>
    <form method="post" action="{{ $editing ? route('karyawan.update',$editing->id_karyawan) : route('karyawan.store') }}">
      @csrf
      @if($editing) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input required name="nama" class="form-control" value="{{ old('nama', $editing->nama ?? '') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Divisi</label>
        <input name="divisi" class="form-control" value="{{ old('divisi', $editing->divisi ?? '') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Jabatan</label>
        <select name="id_jabatan" class="form-select">
          <option value="">-- Pilih --</option>
          @foreach($jabatanList as $j)
            <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $editing->id_jabatan ?? '')==$j->id_jabatan ? 'selected':'' }}>
              {{ $j->jabatan }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Rating</label>
        <select name="id_rating" class="form-select">
          <option value="">-- Pilih --</option>
          @foreach($ratingList as $r)
            <option value="{{ $r->id_rating }}" {{ old('id_rating', $editing->id_rating ?? '')==$r->id_rating ? 'selected':'' }}>
              {{ $r->rating }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Umur</label>
          <input type="number" name="umur" class="form-control" value="{{ old('umur', $editing->umur ?? '') }}">
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="form-select">
            <option {{ old('jenis_kelamin', $editing->jenis_kelamin ?? '')=='Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
            <option {{ old('jenis_kelamin', $editing->jenis_kelamin ?? '')=='Perempuan' ? 'selected' : '' }}>Perempuan</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Status</label>
          <input name="status" class="form-control" value="{{ old('status', $editing->status ?? '') }}">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control">{{ old('alamat', $editing->alamat ?? '') }}</textarea>
      </div>
      <button class="btn btn-primary">{{ $editing ? 'Update' : 'Simpan' }}</button>
      <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection
