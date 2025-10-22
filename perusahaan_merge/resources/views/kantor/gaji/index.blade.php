@extends('layouts.kantor')

@section('title','Gaji')

@section('content')
<div class="d-flex justify-content-between mb-2">
  <h4><i class="fas fa-money-bill-wave me-2"></i>Daftar Gaji</h4>
  <a class="btn btn-success btn-sm" href="{{ route('gaji.create') }}">
    <i class="fas fa-plus me-1"></i>Tambah
  </a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr><th>#</th><th>Karyawan</th><th>Periode</th><th>Total Pendapatan</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @foreach($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>
              {{ optional($r->karyawan)->nama }}
              <br><small class="text-muted">{{ optional(optional($r->karyawan)->jabatan)->jabatan }}</small>
            </td>
            <td>{{ $r->periode }}</td>
            <td>Rp {{ number_format($r->total_pendapatan,0,',','.') }}</td>
            <td>
              <a class="btn btn-sm btn-primary" href="{{ route('gaji.show',$r->id_gaji) }}">
                <i class="fa-solid fa-circle-info"></i>
              </a>
              <a class="btn btn-sm btn-primary" href="{{ route('gaji.edit',$r->id_gaji) }}">
                <i class="fas fa-edit"></i>
              </a>
              <form action="{{ route('gaji.destroy',$r->id_gaji) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus gaji?')">
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
