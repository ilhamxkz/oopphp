@extends('layouts.kantor')

@section('title', ($editing? 'Edit':'Tambah').' Tarif Lembur')

@section('content')
<div class="card">
  <div class="card-body">
    <h5><i class="fas fa-clock me-2"></i>{{ $editing ? 'Edit Tarif Lembur' : 'Tambah Tarif Lembur' }}</h5>
    <form method="post" action="{{ $editing ? route('lembur.update',$editing->id_lembur) : route('lembur.store') }}">
      @csrf
      @if($editing) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Tarif (per jam)</label>
        <input required type="number" step="0.01" name="tarif" class="form-control" value="{{ old('tarif', $editing->tarif ?? '') }}">
      </div>
      <button class="btn btn-primary">{{ $editing ? 'Update' : 'Simpan' }}</button>
      <a href="{{ route('lembur.index') }}" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection
