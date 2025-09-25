<?php
// app/views/karyawan_list.php
// Requires: $karyawanModel
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