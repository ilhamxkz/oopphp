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

// ambil value dengan fallback
$host = $dbCfg['host'] ?? '127.0.0.1';
$dbname = $dbCfg['dbname'] ?? '';
$user = $dbCfg['user'] ?? '';
$pass = $dbCfg['pass'] ?? '';
$charset = $dbCfg['charset'] ?? 'utf8mb4';

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
// HANDLE KARYAWAN CRUD (sudah ada, sedikit sanitasi)
// ================================
if ($page === 'karyawan' && $action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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

if ($page === 'karyawan' && $action === 'edit' && $id && $_SERVER['REQUEST_METHOD']==='POST') {
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

if ($page === 'karyawan' && $action === 'delete' && $id) {
    $karyawanModel->delete($id, 'id_karyawan');
    header("Location: ?page=karyawan");
    exit;
}

// ================================
// HANDLE JABATAN CRUD ACTIONS
// ================================
// Create
if ($page === 'jabatan' && $action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'jabatan' => $_POST['jabatan'] ?? '',
        'gaji_pokok' => floatval($_POST['gaji_pokok'] ?? 0),
        'tunjangan' => floatval($_POST['tunjangan'] ?? 0)
    ];
    $jabatanModel->create($posted);
    header("Location: ?page=jabatan");
    exit;
}

// Edit
if ($page === 'jabatan' && $action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'jabatan' => $_POST['jabatan'] ?? '',
        'gaji_pokok' => floatval($_POST['gaji_pokok'] ?? 0),
        'tunjangan' => floatval($_POST['tunjangan'] ?? 0)
    ];
    $jabatanModel->update($id, $posted);
    header("Location: ?page=jabatan");
    exit;
}

// Delete
if ($page === 'jabatan' && $action === 'delete' && $id) {
    $jabatanModel->delete($id, 'id_jabatan');
    header("Location: ?page=jabatan");
    exit;
}

// ================================
// HANDLE RATING CRUD ACTIONS
// ================================
if ($page === 'rating' && $action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'rating' => $_POST['rating'] ?? '',
        'persentase_bonus' => floatval($_POST['persentase_bonus'] ?? 0)
    ];
    $ratingModel->create($posted);
    header("Location: ?page=rating");
    exit;
}

if ($page === 'rating' && $action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'rating' => $_POST['rating'] ?? '',
        'persentase_bonus' => floatval($_POST['persentase_bonus'] ?? 0)
    ];
    $ratingModel->update($id, $posted);
    header("Location: ?page=rating");
    exit;
}

if ($page === 'rating' && $action === 'delete' && $id) {
    $ratingModel->delete($id, 'id_rating');
    header("Location: ?page=rating");
    exit;
}

// ================================
// HANDLE LEMBUR CRUD ACTIONS
// ================================
if ($page === 'lembur' && $action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'tarif' => floatval($_POST['tarif'] ?? 0)
    ];
    $lemburModel->create($posted);
    header("Location: ?page=lembur");
    exit;
}

if ($page === 'lembur' && $action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'tarif' => floatval($_POST['tarif'] ?? 0)
    ];
    $lemburModel->update($id, $posted);
    header("Location: ?page=lembur");
    exit;
}

if ($page === 'lembur' && $action === 'delete' && $id) {
    $lemburModel->delete($id, 'id_lembur');
    header("Location: ?page=lembur");
    exit;
}

// ================================
// HANDLE GAJI CRUD ACTIONS (TAMBAHAN)
// ================================
// CREATE gaji: hitung komponen lalu simpan
if ($page === 'gaji' && $action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil input
    $id_karyawan = $_POST['id_karyawan'] ?? null;
    $id_lembur = !empty($_POST['id_lembur']) ? $_POST['id_lembur'] : null;
    $periode = $_POST['periode'] ?? date('Y-m');
    $lama_lembur = floatval($_POST['lama_lembur'] ?? 0);

    // ambil data relasi
    $karyawan = $karyawanModel->find($id_karyawan, 'id_karyawan');
    if (!$karyawan) {
        die('Karyawan tidak ditemukan.');
    }

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

// EDIT gaji: gunakan model helper jika ada, atau lakukan update manual via calculateAndUpdate
if ($page === 'gaji' && $action === 'edit' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // jika model punya calculateAndUpdate, gunakan itu. Kalau tidak, implement serupa create
    if (method_exists($gajiModel, 'calculateAndUpdate')) {
        $gajiModel->calculateAndUpdate($id, $_POST);
    } else {
        // fallback: recompute sama seperti create
        $existing = $gajiModel->find($id, 'id_gaji');
        if (!$existing) {
            die('Gaji tidak ditemukan.');
        }

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

// DELETE gaji
if ($page === 'gaji' && $action === 'delete' && $id) {
    $gajiModel->delete($id, 'id_gaji');
    header("Location: ?page=gaji");
    exit;
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
  <style>
    body { min-height:100vh; }
    .sidebar { min-width:240px; max-width:260px; }
    @media (max-width: 768px) { .sidebar { position: fixed; z-index: 1030; left:-300px; transition: left .25s;} .sidebar.show { left:0; } }
    .content { padding:20px; }
  </style>
</head>
<body>
<div class="d-flex">
  <nav class="bg-dark text-white sidebar p-3">
    <h4 class="text-white">Kantor</h4>
    <hr class="text-secondary">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link text-white" href="?page=dashboard">Dashboard</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="?page=karyawan">Daftar Karyawan</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="?page=jabatan">Daftar Jabatan</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="?page=rating">Daftar Rating</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="?page=lembur">Tarif Lembur</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="?page=gaji">Gaji Karyawan</a></li>
    </ul>
  </nav>

  <main class="flex-fill content">
    <?php
    // ROUTES -> include views inline for simplicity

    if ($page === 'dashboard') {
        $latest = $karyawanModel->allWithRelations(10);
        ?>
        <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Selamat datang, Ilham</h2>
            <div><small class="text-muted"><?= date('l, d M Y H:i') ?></small></div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="card mb-3">
                <div class="card-body">
                  <h5 class="card-title">Ringkasan</h5>
                  <p class="card-text">Total Karyawan: <strong><?= count($karyawanModel->all()) ?></strong></p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card mb-3">
                <div class="card-body">
                  <h6>10 Karyawan Terbaru</h6>
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
        if ($action === 'create' || ($action === 'edit' && $id)) {
            // form
            $editing = ($action==='edit' && $id) ? $karyawanModel->find($id, 'id_karyawan') : null;
            $jabatanList = $jabatanModel->all('jabatan');
            $ratingList = $ratingModel->all('rating');
            ?>
            <div class="card">
              <div class="card-body">
                <h5><?= $editing ? "Edit Karyawan" : "Tambah Karyawan" ?></h5>
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
              <h4>Daftar Karyawan</h4>
              <div>
                <a class="btn btn-success btn-sm" href="?page=karyawan&action=create">Tambah</a>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead>
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
                        <a class="btn btn-sm btn-primary" href="?page=karyawan&action=edit&id=<?= $r['id_karyawan'] ?>">Edit</a>
                        <a class="btn btn-sm btn-danger" href="?page=karyawan&action=delete&id=<?= $r['id_karyawan'] ?>" onclick="return confirm('Hapus?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php
        }
    }
    // JABATAN VIEWS
    elseif ($page === 'jabatan') {
        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $jabatanModel->find($id, 'id_jabatan') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><?= $editing ? "Edit Jabatan" : "Tambah Jabatan" ?></h5>
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
              <h4>Daftar Jabatan</h4>
              <a href="?page=jabatan&action=create" class="btn btn-success btn-sm">Tambah</a>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead><tr><th>#</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjangan</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach($rows as $i=>$r): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($r['jabatan']) ?></td>
                    <td><?= number_format($r['gaji_pokok'],2) ?></td>
                    <td><?= number_format($r['tunjangan'],2) ?></td>
                    <td>
                      <a class="btn btn-sm btn-primary" href="?page=jabatan&action=edit&id=<?= $r['id_jabatan'] ?>">Edit</a>
                      <a class="btn btn-sm btn-danger" href="?page=jabatan&action=delete&id=<?= $r['id_jabatan'] ?>" onclick="return confirm('Hapus jabatan?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php
        }
    }
    // RATING VIEWS
    elseif ($page === 'rating') {
        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $ratingModel->find($id, 'id_rating') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><?= $editing ? "Edit Rating" : "Tambah Rating" ?></h5>
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
            <h4>Daftar Rating</h4>
            <a class="btn btn-success btn-sm mb-2" href="?page=rating&action=create">Tambah</a>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead><tr><th>#</th><th>Rating</th><th>% Bonus</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php foreach($rows as $i=>$r): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($r['rating']) ?></td>
                    <td><?= $r['persentase_bonus'] ?>%</td>
                    <td>
                      <a class="btn btn-sm btn-primary" href="?page=rating&action=edit&id=<?= $r['id_rating'] ?>">Edit</a>
                      <a class="btn btn-sm btn-danger" href="?page=rating&action=delete&id=<?= $r['id_rating'] ?>" onclick="return confirm('Hapus rating?')">Delete</a>
                    </td>
                  </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
            </div>
            <?php
        }
    }
    // LEMBUR VIEWS
    elseif ($page === 'lembur') {
        if ($action === 'create' || ($action === 'edit' && $id)) {
            $editing = ($action==='edit' && $id) ? $lemburModel->find($id, 'id_lembur') : null;
            ?>
            <div class="card">
              <div class="card-body">
                <h5><?= $editing ? "Edit Tarif Lembur" : "Tambah Tarif Lembur" ?></h5>
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
            <h4>Tarif Lembur</h4>
            <a class="btn btn-success btn-sm mb-2" href="?page=lembur&action=create">Tambah</a>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead><tr><th>#</th><th>Tarif</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php foreach($rows as $i=>$r): ?>
                    <tr>
                      <td><?= $i+1 ?></td>
                      <td><?= number_format($r['tarif'],2) ?></td>
                      <td>
                        <a class="btn btn-sm btn-primary" href="?page=lembur&action=edit&id=<?= $r['id_lembur'] ?>">Edit</a>
                        <a class="btn btn-sm btn-danger" href="?page=lembur&action=delete&id=<?= $r['id_lembur'] ?>" onclick="return confirm('Hapus tarif lembur?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php
        }
    }
    // GAJI VIEWS
    elseif ($page === 'gaji') {
        if ($action === 'create') {
            $karyawans = $karyawanModel->allWithRelations();
            $lemburs = $lemburModel->all();
            ?>
            <h4>Hitung & Tambah Gaji</h4>
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
                      <option value="<?= $l['id_lembur'] ?>"><?= number_format($l['tarif'],2) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4 mb-2">
                  <label>Periode</label>
                  <input name="periode" class="form-control" value="<?= date('Y-m') ?>">
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-4 mb-2"><label>Lama Lembur (jam)</label><input name="lama_lembur" class="form-control" value="0"></div>
              </div>
              <button class="btn btn-primary mt-2">Hitung & Simpan</button>
            </form>
            <?php
        } else {
            $rows = $gajiModel->allWithRelations();
            ?>
            <div class="d-flex justify-content-between mb-2">
              <h4>Daftar Gaji</h4>
              <a class="btn btn-success btn-sm" href="?page=gaji&action=create">Tambah</a>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead><tr><th>#</th><th>Karyawan</th><th>Periode</th><th>Total Pendapatan</th><th>Actions</th></tr></thead>
                <tbody>
                  <?php foreach($rows as $i=>$r): ?>
                    <tr>
                      <td><?= $i+1 ?></td>
                      <td><?= htmlspecialchars($r['nama']) ?> <br><small class="text-muted"><?= htmlspecialchars($r['jabatan']) ?></small></td>
                      <td><?= $r['periode'] ?></td>
                      <td><?= number_format($r['total_pendapatan'],2) ?></td>
                      <td>
                        <a class="btn btn-sm btn-primary" href="?page=gaji&action=edit&id=<?= $r['id_gaji'] ?>">Edit</a>
                        <a class="btn btn-sm btn-danger" href="?page=gaji&action=delete&id=<?= $r['id_gaji'] ?>" onclick="return confirm('Hapus gaji?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php
        }
    }
    else {
        echo "<p>Halaman tidak ditemukan.</p>";
    }
    ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
