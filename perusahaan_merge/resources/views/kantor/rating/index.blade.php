@extends('layouts.kantor')

@section('title','Rating')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
  <h4><i class="fas fa-star me-2"></i>Daftar Rating</h4>
  <a class="btn btn-success btn-sm" href="{{ route('rating.create') }}">
    <i class="fas fa-plus me-1"></i>Tambah
  </a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr><th>#</th><th>Rating</th><th>% Bonus</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @foreach($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->rating }}</td>
            <td>{{ $r->persentase_bonus }}%</td>
            <td>
              <a class="btn btn-sm btn-primary" href="{{ route('rating.show',$r->id_rating) }}">
                <i class="fa-solid fa-circle-info"></i>
              </a>
              <a class="btn btn-sm btn-primary" href="{{ route('rating.edit',$r->id_rating) }}">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('rating.destroy',$r->id_rating) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus rating?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
