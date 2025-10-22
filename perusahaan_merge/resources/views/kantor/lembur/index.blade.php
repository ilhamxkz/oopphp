@extends('layouts.kantor')

@section('title','Tarif Lembur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
  <h4><i class="fas fa-clock me-2"></i>Tarif Lembur</h4>
  <a class="btn btn-success btn-sm" href="{{ route('lembur.create') }}">
    <i class="fas fa-plus me-1"></i>Tambah
  </a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr><th>#</th><th>Tarif</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @foreach($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>Rp {{ number_format($r->tarif,0,',','.') }}</td>
            <td>
              <a class="btn btn-sm btn-primary" href="{{ route('lembur.show',$r->id_lembur) }}">
                <i class="fa-solid fa-circle-info"></i>
              </a>
              <a class="btn btn-sm btn-primary" href="{{ route('lembur.edit',$r->id_lembur) }}">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('lembur.destroy',$r->id_lembur) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus tarif lembur?')">
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
