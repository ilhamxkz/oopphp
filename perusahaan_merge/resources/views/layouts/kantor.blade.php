<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Admin Kantor')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <!-- Header (mobile only) -->
  <header class="d-lg-none border-bottom bg-white">
    <div class="container-fluid d-flex align-items-center justify-content-between py-2">
      <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Toggle sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <span class="navbar-brand mb-0 h1">Admin Kantor</span>
      <span></span>
    </div>
  </header>

  <div class="d-flex">
    <!-- Sidebar -->
    <div class="offcanvas offcanvas-start offcanvas-lg bg-dark text-white" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel" style="--bs-offcanvas-width: 260px;">
      <div class="offcanvas-header border-bottom border-secondary">
        <div class="d-flex align-items-center gap-2">
          <!-- Placeholder logo area -->
          <div class="bg-secondary rounded" style="width: 40px; height: 40px;"></div>
          <div>
            <div class="fs-6 text-white-50">Logo</div>
            <div class="fw-semibold" id="sidebarLabel">Admin Kantor</div>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-0">
        <nav class="nav nav-pills flex-column">
          <a class="nav-link text-white px-3 py-2" href="{{ route('karyawan.index') }}"><i class="fa-solid fa-users me-2"></i>Karyawan</a>
          <a class="nav-link text-white px-3 py-2" href="{{ route('jabatan.index') }}"><i class="fa-solid fa-briefcase me-2"></i>Jabatan</a>
          <a class="nav-link text-white px-3 py-2" href="{{ route('rating.index') }}"><i class="fa-solid fa-star me-2"></i>Rating</a>
          <a class="nav-link text-white px-3 py-2" href="{{ route('lembur.index') }}"><i class="fa-solid fa-clock me-2"></i>Lembur</a>
          <a class="nav-link text-white px-3 py-2" href="{{ route('gaji.index') }}"><i class="fa-solid fa-money-bill-wave me-2"></i>Gaji</a>
        </nav>
      </div>
    </div>

    <!-- Main content -->
    <main class="flex-grow-1">
      <div class="container-fluid py-4">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
