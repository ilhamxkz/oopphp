<?php
// app/views/jabatan_list.php
// Requires: $jabatanModel
$rows = $jabatanModel->all('created_at DESC');
?>
<div class="d-flex justify-content-between align-items-center mb-2">
<h4>Daftar Jabatan</h4>
<a href="?page=jabatan&action=create" class="btn btn-success btn-sm">Tambah</a>
</div>
<div class="table-responsive">
<table class="table table-bordered">
<thead><tr><th>#</th><th>Jabatan</th><th>Gaji Pokok</th><th>Tunjgan</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($rows as $i=>$r): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($r['jabatan']) ?></td>
<td><?= number_format($r['gaji_pokok'],2) ?></td>
<td><?= number_format($r['tunjangan'],2) ?></td>
<td>
<a class="btn btn-sm btn-primary" href="?page=jabatan&action=edit&id=<?= $r['id_jabatan'] ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="?page=jabatan&action=delete&id=<?= $r['id_jabatan'] ?>" onclick="return confirm('Hapus?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>