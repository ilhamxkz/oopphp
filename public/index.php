<?php
// public/index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// path ke config
$configFile = __DIR__ . '/../app/config.php';
if (!file_exists($configFile)) {
    die('ERROR: config.php tidak ditemukan di path: ' . $configFile);
}

// ambil config. include mengembalikan apa yang di-'return' di file config
$config = include $configFile;
if ($config === false) {
    die('ERROR: Gagal memuat config.php (include returned false). Periksa permission atau isi file.');
}

// dukung config sebagai array atau object
$dbCfg = null;
if (is_array($config) && isset($config['db'])) {
    $dbCfg = $config['db'];
} elseif (is_object($config) && isset($config->db)) {
    $dbCfg = (array)$config->db;
}

if (!$dbCfg) {
    die('ERROR: Konfigurasi DB tidak ditemukan pada config.php (harus ada index/key "db").');
}

// ambil value
$host = $dbCfg['host'];
$dbname = $dbCfg['dbname'];
$user = $dbCfg['user'];
$pass = $dbCfg['pass'];
$charset = $dbCfg['charset'];

if (empty($dbname)) {
    die('ERROR: nama database (dbname) belum diisi di config.php.');
}

$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbname, $charset);

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('PDO Error: ' . $e->getMessage());
}

// Autoload models
spl_autoload_register(function($class){
    $paths = [
        __DIR__ . "/../app/models/$class.php",
        __DIR__ . "/../app/$class.php"
    ];
    foreach($paths as $p) {
        if (file_exists($p)) { require_once $p; return; }
    }
});

// instantiate models (pastikan file model ada dan konstruktor menerima PDO)
$karyawanModel = new Karyawan($pdo);
$jabatanModel = new Jabatan($pdo);
$ratingModel = new Rating($pdo);
$lemburModel = new Lembur($pdo);
$gajiModel = new Gaji($pdo);

// simple router
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// ================================
// HANDLE KARYAWAN CRUD
// ================================
if ($page === 'karyawan') {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = [
            'id_jabatan'=> !empty($_POST['id_jabatan']) ? $_POST['id_jabatan'] : null,
            'id_rating'=> !empty($_POST['id_rating']) ? $_POST['id_rating'] : null,
            'nama'=> $_POST['nama'] ?? '',
            'divisi'=> $_POST['divisi'] ?? '',
            'alamat'=> $_POST['alamat'] ?? '',
            'umur'=> $_POST['umur'] ?? null,
            'jenis_kelamin'=> $_POST['jenis_kelamin'] ?? '',
            'status'=> $_POST['status'] ?? ''
        ];
        $karyawanModel->create($posted);
        header("Location: ?page=karyawan");
        exit;
    }

    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD']==='POST') {
        $posted = [
            'id_jabatan'=> !empty($_POST['id_jabatan']) ? $_POST['id_jabatan'] : null,
            'id_rating'=> !empty($_POST['id_rating']) ? $_POST['id_rating'] : null,
            'nama'=> $_POST['nama'] ?? '',
            'divisi'=> $_POST['divisi'] ?? '',
            'alamat'=> $_POST['alamat'] ?? '',
            'umur'=> $_POST['umur'] ?? null,
            'jenis_kelamin'=> $_POST['jenis_kelamin'] ?? '',
            'status'=> $_POST['status'] ?? ''
        ];
        $karyawanModel->update($id, $posted);
        header("Location: ?page=karyawan");
        exit;
    }

    if ($action === 'delete' && $id) {
        $karyawanModel->delete($id, 'id_karyawan');
        header("Location: ?page=karyawan");
        exit;
    }

    // DETAIL KARYAWAN
    if ($action === 'detail' && $id) {
        $detailKaryawan = $karyawanModel->find($id, 'id_karyawan');
        $jabatanDetail = $detailKaryawan['id_jabatan'] ? $jabatanModel->find($detailKaryawan['id_jabatan'], 'id_jabatan') : null;
        $ratingDetail = $detailKaryawan['id_rating'] ? $ratingModel->find($detailKaryawan['id_rating'], 'id_rating') : null;
    }
}

// ================================
// HANDLE JABATAN CRUD ACTIONS
// ================================
if ($page === 'jabatan') {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = [
            'jabatan' => $_POST['jabatan'] ?? '',
            'gaji_pokok' => floatval($_POST['gaji_pokok'] ?? 0),
            'tunjangan' => floatval($_POST['tunjangan'] ?? 0)
        ];
        $jabatanModel->create($posted);
        header("Location: ?page=jabatan");
        exit;
    }

    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = [
            'jabatan' => $_POST['jabatan'] ?? '',
            'gaji_pokok' => floatval($_POST['gaji_pokok'] ?? 0),
            'tunjangan' => floatval($_POST['tunjangan'] ?? 0)
        ];
        $jabatanModel->update($id, $posted);
        header("Location: ?page=jabatan");
        exit;
    }

    if ($action === 'delete' && $id) {
        $jabatanModel->delete($id, 'id_jabatan');
        header("Location: ?page=jabatan");
        exit;
    }

    // DETAIL JABATAN
    if ($action === 'detail' && $id) {
        $detailJabatan = $jabatanModel->find($id, 'id_jabatan');
    }
}

// ================================
// HANDLE RATING CRUD ACTIONS
// ================================
if ($page === 'rating') {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = [
            'rating' => $_POST['rating'] ?? '',
            'persentase_bonus' => floatval($_POST['persentase_bonus'] ?? 0)
        ];
        $ratingModel->create($posted);
        header("Location: ?page=rating");
        exit;
    }

    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = [
            'rating' => $_POST['rating'] ?? '',
            'persentase_bonus' => floatval($_POST['persentase_bonus'] ?? 0)
        ];
        $ratingModel->update($id, $posted);
        header("Location: ?page=rating");
        exit;
    }

    if ($action === 'delete' && $id) {
        $ratingModel->delete($id, 'id_rating');
        header("Location: ?page=rating");
        exit;
    }

    // DETAIL RATING
    if ($action === 'detail' && $id) {
        $detailRating = $ratingModel->find($id, 'id_rating');
    }
}

// ================================
// HANDLE LEMBUR CRUD ACTIONS
// ================================
if ($page === 'lembur') {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = ['tarif' => floatval($_POST['tarif'] ?? 0)];
        $lemburModel->create($posted);
        header("Location: ?page=lembur");
        exit;
    }

    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $posted = ['tarif' => floatval($_POST['tarif'] ?? 0)];
        $lemburModel->update($id, $posted);
        header("Location: ?page=lembur");
        exit;
    }

    if ($action === 'delete' && $id) {
        $lemburModel->delete($id, 'id_lembur');
        header("Location: ?page=lembur");
        exit;
    }

    // DETAIL LEMBUR
    if ($action === 'detail' && $id) {
        $detailLembur = $lemburModel->find($id, 'id_lembur');
    }
}

// ================================
// HANDLE GAJI CRUD ACTIONS
// ================================
if ($page === 'gaji') {
    if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_karyawan = $_POST['id_karyawan'] ?? null;
        $id_lembur = !empty($_POST['id_lembur']) ? $_POST['id_lembur'] : null;
        $periode = $_POST['periode'] ?? date('Y-m');
        $lama_lembur = floatval($_POST['lama_lembur'] ?? 0);

        $karyawan = $karyawanModel->find($id_karyawan, 'id_karyawan');
        if (!$karyawan) die('Karyawan tidak ditemukan.');
        $jabatan = $karyawan['id_jabatan'] ? $jabatanModel->find($karyawan['id_jabatan'], 'id_jabatan') : null;
        $rating = $karyawan['id_rating'] ? $ratingModel->find($karyawan['id_rating'], 'id_rating') : null;
        $lembur = $id_lembur ? $lemburModel->find($id_lembur, 'id_lembur') : null;

        $gaji_pokok = $jabatan['gaji_pokok'] ?? 0;
        $tunjangan = $jabatan['tunjangan'] ?? 0;
        $persentase_bonus = $rating['persentase_bonus'] ?? 0;
        $tarif_lembur = $lembur['tarif'] ?? 0;

        $bonus = ($gaji_pokok + $tunjangan) * ($persentase_bonus / 100.0);
        $total_lembur = $tarif_lembur * $lama_lembur;
        $total_pendapatan = $gaji_pokok + $tunjangan + $bonus + $total_lembur;

        $payload = [
            'id_karyawan' => $id_karyawan,
            'periode' => $periode,
            'gaji_pokok' => $gaji_pokok,
            'tunjangan' => $tunjangan,
            'persentase_bonus' => $persentase_bonus,
            'bonus' => $bonus,
            'id_lembur' => $id_lembur,
            'lama_lembur' => $lama_lembur,
            'total_lembur' => $total_lembur,
            'total_pendapatan' => $total_pendapatan,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $gajiModel->create($payload);
        header("Location: ?page=gaji");
        exit;
    }

    if ($action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (method_exists($gajiModel, 'calculateAndUpdate')) {
            $gajiModel->calculateAndUpdate($id, $_POST);
        } else {
            $existing = $gajiModel->find($id, 'id_gaji');
            if (!$existing) die('Gaji tidak ditemukan.');

            $id_karyawan = $_POST['id_karyawan'] ?? $existing['id_karyawan'];
            $id_lembur = !empty($_POST['id_lembur']) ? $_POST['id_lembur'] : $existing['id_lembur'];
            $periode = $_POST['periode'] ?? $existing['periode'];
            $lama_lembur = floatval($_POST['lama_lembur'] ?? $existing['lama_lembur'] ?? 0);

            $karyawan = $karyawanModel->find($id_karyawan, 'id_karyawan');
            $jabatan = $karyawan['id_jabatan'] ? $jabatanModel->find($karyawan['id_jabatan'], 'id_jabatan') : null;
            $rating = $karyawan['id_rating'] ? $ratingModel->find($karyawan['id_rating'], 'id_rating') : null;
            $lembur = $id_lembur ? $lemburModel->find($id_lembur, 'id_lembur') : null;

            $gaji_pokok = $jabatan['gaji_pokok'] ?? 0;
            $tunjangan = $jabatan['tunjangan'] ?? 0;
            $persentase_bonus = $rating['persentase_bonus'] ?? 0;
            $tarif_lembur = $lembur['tarif'] ?? 0;

            $bonus = ($gaji_pokok + $tunjangan) * ($persentase_bonus / 100.0);
            $total_lembur = $tarif_lembur * $lama_lembur;
            $total_pendapatan = $gaji_pokok + $tunjangan + $bonus + $total_lembur;

            $payload = [
                'id_karyawan' => $id_karyawan,
                'periode' => $periode,
                'gaji_pokok' => $gaji_pokok,
                'tunjangan' => $tunjangan,
                'persentase_bonus' => $persentase_bonus,
                'bonus' => $bonus,
                'id_lembur' => $id_lembur,
                'lama_lembur' => $lama_lembur,
                'total_lembur' => $total_lembur,
                'total_pendapatan' => $total_pendapatan,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $gajiModel->update($id, $payload);
        }

        header("Location: ?page=gaji");
        exit;
    }

    if ($action === 'delete' && $id) {
        $gajiModel->delete($id, 'id_gaji');
        header("Location: ?page=gaji");
        exit;
    }

    // DETAIL GAJI
    if ($action === 'detail' && $id) {
        $detailGaji = $gajiModel->find($id, 'id_gaji');
        $karyawanDetail = $detailGaji['id_karyawan'] ? $karyawanModel->find($detailGaji['id_karyawan'], 'id_karyawan') : null;
        $jabatanDetail = $karyawanDetail['id_jabatan'] ? $jabatanModel->find($karyawanDetail['id_jabatan'], 'id_jabatan') : null;
        $ratingDetail = $karyawanDetail['id_rating'] ? $ratingModel->find($karyawanDetail['id_rating'], 'id_rating') : null;
        $lemburDetail = $detailGaji['id_lembur'] ? $lemburModel->find($detailGaji['id_lembur'], 'id_lembur') : null;
    }
}

/* Layout header */
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Kantor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body { 
      min-height: 100vh; 
      background-color: #f8f9fa;
    }
    
    .sidebar { 
      min-width: 260px; 
      max-width: 260px; 
      background: #343a40;
      color: white;
      transition: all 0.3s ease;
      position: fixed;
      height: 100vh;
      top: 0;
      left: 0;
      z-index: 1050;
      overflow-y: auto;
    }
    
    .sidebar .nav-link { 
      color: rgba(255,255,255,0.8); 
      padding: 12px 20px;
      border-radius: 0;
      transition: all 0.2s ease;
    }
    
    .sidebar .nav-link:hover, 
    .sidebar .nav-link.active { 
      color: white; 
      background-color: #495057;
    }
    
    .sidebar .nav-link i {
      width: 20px;
      margin-right: 10px;
    }
    
    .main-content { 
      margin-left: 260px; 
      padding: 20px;
      transition: all 0.3s ease;
      min-height: 100vh;
    }
    
    .navbar-toggler {
      display: none;
    }
    
    .sidebar-brand {
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid #495057;
      margin-bottom: 10px;
    }
    
    .sidebar-brand h4 {
      margin: 0;
      color: white;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) { 
      .sidebar { 
        left: -260px; 
      }
      
      .sidebar.show { 
        left: 0; 
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .navbar-toggler {
        display: inline-block;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1051;
        background: #007bff;
        border: none;
        color: white;
        padding: 8px 12px;
        border-radius: 5px;
      }
      
      .content-with-toggle {
        padding-top: 60px;
      }
      
      /* Overlay untuk mobile */
      .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
      }
      
      .sidebar-overlay.show {
        display: block;
      }
    }
    
    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      border: 1px solid rgba(0,0,0,0.125);
    }
  </style>
</head>
<body>

<!-- Mobile Toggle Button -->
<button class="navbar-toggler" type="button" onclick="toggleSidebar()">
  <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay untuk Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <h4><i class="fas fa-building"></i> Admin Kantor</h4>
  </div>
  
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>" href="?page=dashboard">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $page === 'karyawan' ? 'active' : '' ?>" href="?page=karyawan">
        <i class="fas fa-users"></i> Karyawan
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $page === 'jabatan' ? 'active' : '' ?>" href="?page=jabatan">
        <i class="fas fa-briefcase"></i> Jabatan
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $page === 'rating' ? 'active' : '' ?>" href="?page=rating">
        <i class="fas fa-star"></i> Rating
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $page === 'lembur' ? 'active' : '' ?>" href="?page=lembur">
        <i class="fas fa-clock"></i> Lembur
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $page === 'gaji' ? 'active' : '' ?>" href="?page=gaji">
        <i class="fas fa-money-bill-wave"></i> Gaji
      </a>
    </li>
  </ul>
</nav>

<!-- Main Content -->
<main class="main-content">
  <div class="content-with-toggle">
    <?php
    // ROUTES -> include views inline for simplicity

    if ($page === 'dashboard') {
        $latest = $karyawanModel->allWithRelations(10);
        ?>
        <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Selamat datang, Ilham</h2>
            <div><small class="text-muted"><?= date('l, d M Y H:i') ?></small></div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="card mb-3">
                <div class="card-body">
                  <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>Ringkasan</h5>
                  <p class="card-text">Total Karyawan: <strong><?= count($karyawanModel->all()) ?></strong></p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card mb-3">
                <div class="card-body">
                  <h6><i class="fas fa-user-friends me-2"></i>10 Karyawan Terbaru</h6>
                  <ul class="list-unstyled">
                    <?php foreach($latest as $k): ?>
                      <li class="mb-2">
                        <strong><?= htmlspecialchars($k['nama']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($k['jabatan'] ?? '-') ?> — <?= htmlspecialchars($k['divisi']) ?></small>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
    }
    elseif ($page === 'karyawan') {
        if ($action === 'detail' && $id) {
        ?>
        <div class="card">
          <div class="card-body">
            <h4><i class="fas fa-user me-2"></i>Detail Karyawan</h4>
            <p><strong>Nama:</strong> <?= htmlspecialchars($detailKaryawan['nama']) ?></p>
            <p><strong>Divisi:</strong> <?= htmlspecialchars($detailKaryawan['divisi']) ?></p>
            <p><strong>Jabatan:</strong> <?= htmlspecialchars($jabatanDetail['jabatan'] ?? '-') ?></p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($ratingDetail['rating'] ?? '-') ?></p>
            <p><strong>Umur:</strong> <?= $detailKaryawan['umur'] ?></p>
            <p><strong>Jenis Kelamin:</strong> <?= $detailKaryawan['jenis_kelamin'] ?></p>
            <p><strong>Status:</strong> <?= $detailKaryawan['status'] ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($detailKaryawan['alamat']) ?></p>
            <a href="?page=karyawan" class="btn btn-secondary mt-2">Kembali</a>
          </div>
        </div>
        <?php
    } else if ($action === 'create' || ($action === 'edit' && $id)) {
            // form
            $editing = ($action==='edit' && $id) ? $karyawanModel->find($id, 'id_karyawan') : null;
            $jabatanList = $jabatanModel->all('jabatan');
            $ratingList = $ratingModel->all('rating');
            ?>
            <div class="card">
              <div class="card-body">
                <h5><i class="fas fa-user-plus me-2"></i><?= $editing ? "Edit Karyawan" : "Tambah Karyawan" ?></h5>
                <form method="post" action="?page=karyawan&action=<?= $editing ? "edit&id=".$editing['id_karyawan'] : "create" ?>">
                  <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input required name="nama" class="form-control" value="<?= htmlspecialchars($editing['nama'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Divisi</label>
                    <input name="divisi" class="form-control" value="<?= htmlspecialchars($editing['divisi'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Jabatan</label>
                    <select name="id_jabatan" class="form-select">
                      <option value="">-- Pilih --</option>
                      <?php foreach($jabatanList as $j): ?>
                        <option value="<?= $j['id_jabatan'] ?>" <?= (isset($editing['id_jabatan']) && $editing['id_jabatan']==$j['id_jabatan'])?'selected':'' ?>>
                          <?= htmlspecialchars($j['jabatan']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <select name="id_rating" class="form-select">
                      <option value="">-- Pilih --</option>
                      <?php foreach($ratingList as $r): ?>
                        <option value="<?= $r['id_rating'] ?>" <?= (isset($editing['id_rating']) && $editing['id_rating']==$r['id_rating'])?'selected':'' ?>>
                          <?= htmlspecialchars($r['rating']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Umur</label>
                      <input type="number" name="umur" class="form-control" value="<?= htmlspecialchars($editing['umur'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Jenis Kelamin</label>
                      <select name="jenis_kelamin" class="form-select">
                        <option <?= (isset($editing['jenis_kelamin']) && $editing['jenis_kelamin']=='Laki-laki')?'selected':'' ?>>Laki-laki</option>
                        <option <?= (isset($editing['jenis_kelamin']) && $editing['jenis_kelamin']=='Perempuan')?'selected':'' ?>>Perempuan</option>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">Status</label>
                      <input name="status" class="form-control" value="<?= htmlspecialchars($editing['status'] ?? '') ?>">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control"><?= htmlspecialchars($editing['alamat'] ?? '') ?></textarea>
                  </div>

                  <button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
                  <a href="?page=karyawan" class="btn btn-secondary">Batal</a>
                </form>
              </div>
            </div>
            <?php
        } else {
            // list
            $rows = $karyawanModel->allWithRelations();
            ?>
            <div class="d-flex mb-2 justify-content-between align-items-center">
              <h4><i class="fas fa-users me-2"></i>Daftar Karyawan</h4>
              <div>
                <a class="btn btn-success btn-sm" href="?page=karyawan&action=create">
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
                      <?php foreach($rows as $i => $r): ?>
                        <tr>
                          <td><?= $i+1 ?></td>
                          <td><?= htmlspecialchars($r['nama']) ?></td>
                          <td><?= htmlspecialchars($r['jabatan'] ?? '-') ?></td>
                          <td><?= htmlspecialchars($r['divisi']) ?></td>
                          <td><?= htmlspecialchars($r['umur']) ?></td>
                          <td><?= htmlspecialchars($r['rating'] ?? '-') ?></td>
                          <td>
                            <a class="btn btn-sm btn-primary" href="?page=karyawan&action=detail&id=<?= $r['id_karyawan'] ?>">
                              <i class="fa-solid fa-circle-info"></i>
                            </a>
                            <a class="btn btn-sm btn-primary" href="?page=karyawan&action=edit&id=<?= $r['id_karyawan'] ?>">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-sm btn-danger" href="?page=karyawan&action=delete&id=<?= $r['id_karyawan'] ?>" onclick="return confirm('Hapus?')">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php
        }
    }
    // JABATAN VIEWS
    elseif ($page === 'jabatan') {
      if ($action === 'detail' && $id) {
    ?>
    <div class="card">
      <div class="card-body">
        <h4><i class="fas fa-briefcase me-2"></i>Detail Jabatan</h4>
        <p><strong>Jabatan:</strong> <?= htmlspecialchars($detailJabatan['jabatan']) ?></p>
        <p><strong>Gaji Pokok:</strong> Rp <?= number_format($detailJabatan['gaji_pokok'],0,',','.') ?></p>
        <p><strong>Tunjangan:</strong> Rp <?= number_format($detailJabatan['tunjangan'],0,',','.') ?></p>
        <a href="?page=jabatan" class="btn btn-secondary mt-2">Kembali</a>
      </div>
    </div>
    <?php
} else

        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $jabatanModel->find($id, 'id_jabatan') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><i class="fas fa-briefcase me-2"></i><?= $editing ? "Edit Jabatan" : "Tambah Jabatan" ?></h5>
                <form method="post" action="?page=jabatan&action=<?= $editing ? "edit&id=".$editing['id_jabatan'] : "create" ?>">
                  <div class="mb-3">
                    <label class="form-label">Jabatan</label>
                    <input required name="jabatan" class="form-control" value="<?= htmlspecialchars($editing['jabatan'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Gaji Pokok</label>
                    <input type="number" step="0.01" name="gaji_pokok" class="form-control" value="<?= htmlspecialchars($editing['gaji_pokok'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Tunjangan</label>
                    <input type="number" step="0.01" name="tunjangan" class="form-control" value="<?= htmlspecialchars($editing['tunjangan'] ?? '') ?>">
                  </div>
                  <button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
                  <a href="?page=jabatan" class="btn btn-secondary">Batal</a>
                </form>
              </div>
            </div>
            <?php
        } else {
            $rows = $jabatanModel->all('created_at DESC');
            ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4><i class="fas fa-briefcase me-2"></i>Daftar Jabatan</h4>
              <a href="?page=jabatan&action=create" class="btn btn-success btn-sm">
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
                    <?php foreach($rows as $i=>$r): ?>
                      <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($r['jabatan']) ?></td>
                        <td>Rp <?= number_format($r['gaji_pokok'],0,',','.') ?></td>
                        <td>Rp <?= number_format($r['tunjangan'],0,',','.') ?></td>
                        <td>
                          <a class="btn btn-sm btn-primary" href="?page=jabatan&action=detail&id=<?= $r['id_jabatan'] ?>">
                              <i class="fa-solid fa-circle-info"></i>
                            </a>
                          <a class="btn btn-sm btn-primary" href="?page=jabatan&action=edit&id=<?= $r['id_jabatan'] ?>">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a class="btn btn-sm btn-danger" href="?page=jabatan&action=delete&id=<?= $r['id_jabatan'] ?>" onclick="return confirm('Hapus jabatan?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php
        }
    }
    // RATING VIEWS
    elseif ($page === 'rating') {
      if ($action === 'detail' && $id) {
    ?>
    <div class="card">
      <div class="card-body">
        <h4><i class="fas fa-star me-2"></i>Detail Rating</h4>
        <p><strong>id_rating:</strong> <?= htmlspecialchars($detailRating['id_rating']) ?></p>
        <p><strong>Rating:</strong> <?= htmlspecialchars($detailRating['rating']) ?></p>
        <p><strong>Persentase Bonus:</strong> <?= number_format($detailRating['persentase_bonus'], 2) ?>%</p>
        <a href="?page=rating" class="btn btn-secondary mt-2">Kembali</a>
      </div>
    </div>
    <?php
}
 else
        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $ratingModel->find($id, 'id_rating') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><i class="fas fa-star me-2"></i><?= $editing ? "Edit Rating" : "Tambah Rating" ?></h5>
                <form method="post" action="?page=rating&action=<?= $editing ? "edit&id=".$editing['id_rating'] : "create" ?>">
                  <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <input required name="rating" class="form-control" value="<?= htmlspecialchars($editing['rating'] ?? '') ?>">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">% Bonus</label>
                    <input type="number" step="0.01" name="persentase_bonus" class="form-control" value="<?= htmlspecialchars($editing['persentase_bonus'] ?? '') ?>">
                  </div>
                  <button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
                  <a href="?page=rating" class="btn btn-secondary">Batal</a>
                </form>
              </div>
            </div>
            <?php
        } else {
            $rows = $ratingModel->all('id_rating DESC');
            ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4><i class="fas fa-star me-2"></i>Daftar Rating</h4>
              <a class="btn btn-success btn-sm" href="?page=rating&action=create">
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
                      <?php foreach($rows as $i=>$r): ?>
                      <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($r['rating']) ?></td>
                        <td><?= $r['persentase_bonus'] ?>%</td>
                        <td>
                          <a class="btn btn-sm btn-primary" href="?page=rating&action=detail&id=<?= $r['id_rating'] ?>">
                              <i class="fa-solid fa-circle-info"></i>
                            </a>
                          <a class="btn btn-sm btn-primary" href="?page=rating&action=edit&id=<?= $r['id_rating'] ?>">
                            <i class="fas fa-edit"></i>
                          </a>
                          <a class="btn btn-sm btn-danger" href="?page=rating&action=delete&id=<?= $r['id_rating'] ?>" onclick="return confirm('Hapus rating?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                      <?php endforeach;?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php
        }
    }
    // LEMBUR VIEWS
    elseif ($page === 'lembur') {
      if ($action === 'detail' && $id) {
    ?>
    <div class="card">
      <div class="card-body">
        <h4><i class="fas fa-clock me-2"></i>Detail Lembur</h4>
        <p><strong>id lembur:</strong> <?= htmlspecialchars($detailLembur['id_lembur']) ?></p>
        <p><strong>tarif:</strong> Rp <?= number_format($detailLembur['tarif'],0,',','.') ?></p>
        <a href="?page=lembur" class="btn btn-secondary mt-2">Kembali</a>
      </div>
    </div>
    <?php
}
 else
        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $lemburModel->find($id, 'id_lembur') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><i class="fas fa-clock me-2"></i><?= $editing ? "Edit Tarif Lembur" : "Tambah Tarif Lembur" ?></h5>
                <form method="post" action="?page=lembur&action=<?= $editing ? "edit&id=".$editing['id_lembur'] : "create" ?>">
                  <div class="mb-3">
                    <label class="form-label">Tarif (per jam)</label>
                    <input required type="number" step="0.01" name="tarif" class="form-control" value="<?= htmlspecialchars($editing['tarif'] ?? '') ?>">
                  </div>
                  <button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
                  <a href="?page=lembur" class="btn btn-secondary">Batal</a>
                </form>
              </div>
            </div>
            <?php
        } else {
            $rows = $lemburModel->all('id_lembur DESC');
            ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h4><i class="fas fa-clock me-2"></i>Tarif Lembur</h4>
              <a class="btn btn-success btn-sm" href="?page=lembur&action=create">
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
                      <?php foreach($rows as $i=>$r): ?>
                        <tr>
                          <td><?= $i+1 ?></td>
                          <td>Rp <?= number_format($r['tarif'],0,',','.') ?></td>
                          <td>
                            <a class="btn btn-sm btn-primary" href="?page=lembur&action=detail&id=<?= $r['id_lembur'] ?>">
                              <i class="fa-solid fa-circle-info"></i>
                            </a>
                            <a class="btn btn-sm btn-primary" href="?page=lembur&action=edit&id=<?= $r['id_lembur'] ?>">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-sm btn-danger" href="?page=lembur&action=delete&id=<?= $r['id_lembur'] ?>" onclick="return confirm('Hapus tarif lembur?')">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php
        }
    }
    // GAJI VIEWS
    elseif ($page === 'gaji') {
      if ($action === 'detail' && $id) {
    ?>
    <div class="card">
  <div class="card-body">
    <h4><i class="fas fa-wallet me-2"></i>Detail Gaji</h4>

    <p><strong>ID Gaji:</strong> <?= htmlspecialchars($detailGaji['id_gaji'] ?? '-') ?></p>
<p><strong>Nama Karyawan:</strong> <?= htmlspecialchars($karyawanDetail['nama'] ?? '-') ?></p>
    <p><strong>Gaji Pokok:</strong> Rp <?= number_format($detailGaji['gaji_pokok'] ?? 0, 0, ',', '.') ?></p>
    <p><strong>ID Lembur:</strong> <?= htmlspecialchars($detailGaji['id_lembur'] ?? '-') ?></p>
    <p><strong>Periode:</strong> <?= htmlspecialchars($detailGaji['periode'] ?? '-') ?></p>
    <p><strong>Lama Lembur:</strong> <?= htmlspecialchars($detailGaji['lama_lembur'] ?? '-') ?> jam</p>
    <p><strong>Total Lembur:</strong> Rp <?= number_format($detailGaji['total_lembur'] ?? 0, 0, ',', '.') ?></p>
    <p><strong>Total Bonus:</strong> Rp <?= number_format($detailGaji['total_bonus'] ?? 0, 0, ',', '.') ?></p>
    <p><strong>Total Tunjangan:</strong> Rp <?= number_format($detailGaji['total_tunjangan'] ?? 0, 0, ',', '.') ?></p>
    <p><strong>Total Pendapatan:</strong> Rp <?= number_format($detailGaji['total_pendapatan'] ?? 0, 0, ',', '.') ?></p>

    <a href="?page=gaji" class="btn btn-secondary mt-2">Kembali</a>
  </div>
</div>

    <?php
}
 else
        if ($action === 'create') {
            $karyawans = $karyawanModel->allWithRelations();
            $lemburs = $lemburModel->all();
            ?>
            <div class="card">
              <div class="card-body">
                <h4><i class="fas fa-money-bill-wave me-2"></i>Hitung & Tambah Gaji</h4>
                <form method="post" action="?page=gaji&action=create">
                  <div class="row">
                    <div class="col-md-4 mb-2">
                      <label>Nama Karyawan</label>
                      <select name="id_karyawan" required class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach($karyawans as $k): ?>
                          <option value="<?= $k['id_karyawan'] ?>"><?= htmlspecialchars($k['nama']) ?> - <?= htmlspecialchars($k['jabatan']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 mb-2">
                      <label>Tarif Lembur</label>
                      <select name="id_lembur" class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach($lemburs as $l): ?>
                          <option value="<?= $l['id_lembur'] ?>">Rp <?= number_format($l['tarif'],0,',','.') ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4 mb-2">
                      <label>Periode</label>
                      <input name="periode" class="form-control" value="<?= date('Y-m') ?>">
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-md-4 mb-2">
                      <label>Lama Lembur (jam)</label>
                      <input name="lama_lembur" class="form-control" value="0">
                    </div>
                  </div>
                  <button class="btn btn-primary mt-2">
                    <i class="fas fa-calculator me-1"></i>Hitung & Simpan
                  </button>
                  <a href="?page=gaji" class="btn btn-secondary mt-2">Batal</a>
                </form>
              </div>
            </div>
            <?php
        } else {
            $rows = $gajiModel->allWithRelations();
            ?>
            <div class="d-flex justify-content-between mb-2">
              <h4><i class="fas fa-money-bill-wave me-2"></i>Daftar Gaji</h4>
              <a class="btn btn-success btn-sm" href="?page=gaji&action=create">
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
                      <?php foreach($rows as $i=>$r): ?>
                        <tr>
                          <td><?= $i+1 ?></td>
                          <td>
                            <?= htmlspecialchars($r['nama']) ?> 
                            <br><small class="text-muted"><?= htmlspecialchars($r['jabatan']) ?></small>
                          </td>
                          <td><?= $r['periode'] ?></td>
                          <td>Rp <?= number_format($r['total_pendapatan'],0,',','.') ?></td>
                          <td>
                            <a class="btn btn-sm btn-primary" href="?page=gaji&action=detail&id=<?= $r['id_gaji'] ?>">
                              <i class="fa-solid fa-circle-info"></i>
                            </a>
                            <a class="btn btn-sm btn-primary" href="?page=gaji&action=edit&id=<?= $r['id_gaji'] ?>">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-sm btn-danger" href="?page=gaji&action=delete&id=<?= $r['id_gaji'] ?>" onclick="return confirm('Hapus gaji?')">
                              <i class="fas fa-trash"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <?php
        }
    }
    else {
        echo "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle me-2'></i>Halaman tidak ditemukan.</div>";
    }
    ?>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  
  sidebar.classList.toggle('show');
  overlay.classList.toggle('show');
}

function closeSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  
  sidebar.classList.remove('show');
  overlay.classList.remove('show');
}

// Close sidebar when clicking on links (for mobile)
document.addEventListener('DOMContentLoaded', function() {
  const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
  
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        closeSidebar();
      }
    });
  });
});

// Handle window resize
window.addEventListener('resize', function() {
  if (window.innerWidth > 768) {
    closeSidebar();
  }
});
</script>
</body>
</html>






