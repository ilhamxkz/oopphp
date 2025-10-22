@extends('layouts.kantor')

@section('title','Karyawan')

@section('content')
<div class="d-flex mb-2 justify-content-between align-items-center">
  <h4><i class="fas fa-users me-2"></i>Daftar Karyawan</h4>
  <div>
    <a class="btn btn-success btn-sm" href="{{ route('karyawan.create') }}">
      <i class="fas fa-plus me-1"></i>Tambah
    </a>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th><th>Nama</th><th>Jabatan</th><th>Divisi</th><th>Umur</th><th>Rating</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $i => $r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->nama }}</td>
            <td>{{ optional($r->jabatan)->jabatan ?? '-' }}</td>
            <td>{{ $r->divisi }}</td>
            <td>{{ $r->umur }}</td>
            <td>{{ optional($r->rating)->rating ?? '-' }}</td>
            <td>
              <a class="btn btn-sm btn-primary" href="{{ route('karyawan.show',$r->id_karyawan) }}">
                <i class="fa-solid fa-circle-info"></i>
              </a>
              <a class="btn btn-sm btn-primary" href="{{ route('karyawan.edit',$r->id_karyawan) }}">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('karyawan.destroy',$r->id_karyawan) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus?')">
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
