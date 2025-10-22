@extends('layouts.kantor')

@section('title','Welcome')

@section('content')
<div class="p-4 bg-light rounded">
  <h3>Selamat datang di Admin Kantor</h3>
  <p class="mb-0">Gunakan menu di atas untuk mengelola data.</p>
</div>

<div class="card mt-4">
  <div class="card-body">
    <div class="d-flex mb-2 justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fas fa-users me-2"></i>10 Karyawan Terbaru</h5>
      <a class="btn btn-sm btn-outline-primary" href="{{ route('karyawan.index') }}">Lihat semua</a>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered mb-0">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Divisi</th>
            <th>Umur</th>
            <th>Rating</th>
            <th>Dibuat</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestKaryawan as $i => $r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>
              <a href="{{ route('karyawan.show', $r->id_karyawan) }}">{{ $r->nama }}</a>
            </td>
            <td>{{ optional($r->jabatan)->jabatan ?? '-' }}</td>
            <td>{{ $r->divisi }}</td>
            <td>{{ $r->umur }}</td>
            <td>{{ optional($r->rating)->rating ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center">Belum ada data karyawan</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
