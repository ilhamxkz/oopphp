@extends('layouts.kantor')

@section('title', ($editing? 'Edit':'Tambah').' Jabatan')

@section('content')
<div class="card">
  <div class="card-body">
    <h5><i class="fas fa-briefcase me-2"></i>{{ $editing ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h5>
    <form method="post" action="{{ $editing ? route('jabatan.update',$editing->id_jabatan) : route('jabatan.store') }}">
      @csrf
      @if($editing) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Jabatan</label>
        <input required name="jabatan" class="form-control" value="{{ old('jabatan', $editing->jabatan ?? '') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Gaji Pokok</label>
        <input type="number" step="0.01" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', $editing->gaji_pokok ?? '') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Tunjangan</label>
        <input type="number" step="0.01" name="tunjangan" class="form-control" value="{{ old('tunjangan', $editing->tunjangan ?? '') }}">
      </div>
      <button class="btn btn-primary">{{ $editing ? 'Update' : 'Simpan' }}</button>
      <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection
