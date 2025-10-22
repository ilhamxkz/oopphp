@extends('layouts.kantor')

@section('title', ($editing? 'Edit':'Tambah').' Rating')

@section('content')
<div class="card">
  <div class="card-body">
    <h5><i class="fas fa-star me-2"></i>{{ $editing ? 'Edit Rating' : 'Tambah Rating' }}</h5>
    <form method="post" action="{{ $editing ? route('rating.update',$editing->id_rating) : route('rating.store') }}">
      @csrf
      @if($editing) @method('PUT') @endif
      <div class="mb-3">
        <label class="form-label">Rating</label>
        <input required name="rating" class="form-control" value="{{ old('rating', $editing->rating ?? '') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">% Bonus</label>
        <input type="number" step="0.01" name="persentase_bonus" class="form-control" value="{{ old('persentase_bonus', $editing->persentase_bonus ?? '') }}">
      </div>
      <button class="btn btn-primary">{{ $editing ? 'Update' : 'Simpan' }}</button>
      <a href="{{ route('rating.index') }}" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
@endsection
