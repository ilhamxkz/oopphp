<?php
// app/views/gaji_list.php
// Requires: $gajiModel
$rows = $gajiModel->allWithRelations();
?>
<div class="d-flex justify-content-between align-items-center mb-2">
<h4>Daftar Gaji Karyawan</h4>
<a href="?page=gaji&action=create" class="btn btn-success btn-sm">Hitung Gaji Baru</a>
</div>
<div class="table-responsive">
<table class="table table-striped table-bordered">
<thead>
<tr>
<th>#</th>
<th>Karyawan</th>
<th>Jabatan</th>
<th>Periode</th>
<th>Lembur (Jam)</th>
<th>Total Lembur</th>
<th>Total Bonus</th>
<th>Total Tunjangan</th>
<th>Total Gaji</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach($rows as $i=>$r): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['jabatan'] ?? '-') ?></td>
<td><?= date('M Y', strtotime($r['periode'].'-01')) ?></td>
<td><?= number_format($r['lama_lembur'],1) ?></td>
<td>Rp <?= number_format($r['total_lembur'],0,',','.') ?></td>
<td>Rp <?= number_format($r['total_bonus'],0,',','.') ?></td>
<td>Rp <?= number_format($r['total_tunjangan'],0,',','.') ?></td>
<td><strong>Rp <?= number_format($r['total_pendapatan'],0,',','.') ?></strong></td>
<td>
<a class="btn btn-sm btn-info" href="?page=gaji&action=detail&id=<?= $r['id_gaji'] ?>">Detail</a>
<a class="btn btn-sm btn-primary" href="?page=gaji&action=edit&id=<?= $r['id_gaji'] ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="?page=gaji&action=delete&id=<?= $r['id_gaji'] ?>" onclick="return confirm('Hapus data gaji ini?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?php if(empty($rows)): ?>
<div class="text-center py-4">
<p class="text-muted">Belum ada data gaji karyawan.</p>
<a href="?page=gaji&action=create" class="btn btn-primary">Hitung Gaji Pertama</a>
</div>
<?php endif; ?>