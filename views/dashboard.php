<?php
// app/views/dashboard.php
// Requires: $karyawanModel
$latest = $karyawanModel->allWithRelations(10);
$totalKaryawan = count($karyawanModel->all());
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
<p class="card-text">Total Karyawan: <strong><?= $totalKaryawan ?></strong></p>
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