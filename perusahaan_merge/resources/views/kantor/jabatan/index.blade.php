@extends('layouts.kantor')

@section('title','Jabatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-2">
  <h4><i class="fas fa-briefcase me-2"></i>Daftar Jabatan</h4>
  <a href="{{ route('jabatan.create') }}" class="btn btn-success btn-sm">
    <i class="fas fa-plus me-1"></i>Tambah
  </a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr><th>#</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @foreach($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->jabatan }}</td>
            <td>Rp {{ number_format($r->gaji_pokok,0,',','.') }}</td>
            <td>Rp {{ number_format($r->tunjangan,0,',','.') }}</td>
            <td>
              <a class="btn btn-sm btn-primary" href="{{ route('jabatan.show',$r->id_jabatan) }}">
                <i class="fa-solid fa-circle-info"></i>
              </a>
              <a class="btn btn-sm btn-primary" href="{{ route('jabatan.edit',$r->id_jabatan) }}">
                <i class="fas fa-edit"></i>
              </a>
              <form class="d-inline" action="{{ route('jabatan.destroy',$r->id_jabatan) }}" method="post" onsubmit="return confirm('Hapus jabatan?')">
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
