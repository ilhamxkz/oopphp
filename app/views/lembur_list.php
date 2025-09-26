<?php
// app/views/lembur_list.php
// Requires: $lemburModel
$rows = $lemburModel->all('created_at DESC');
?>
<div class="d-flex justify-content-between align-items-center mb-2">
<h4>Daftar Tarif Lembur</h4>
<a href="?page=lembur&action=create" class="btn btn-success btn-sm">Tambah</a>
</div>
<div class="table-responsive">
<table class="table table-bordered">
<thead><tr><th>#</th><th>Tarif per Jam</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($rows as $i=>$r): ?>
<tr>
<td><?= $i+1 ?></td>
<td>Rp <?= number_format($r['tarif'],0,',','.') ?></td>
<td>
<a class="btn btn-sm btn-primary" href="?page=lembur&action=edit&id=<?= $r['id_lembur'] ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="?page=lembur&action=delete&id=<?= $r['id_lembur'] ?>" onclick="return confirm('Hapus?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>