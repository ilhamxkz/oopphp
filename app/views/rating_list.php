<?php
// app/views/rating_list.php
// Requires: $ratingModel
$rows = $ratingModel->all('created_at DESC');
?>
<div class="d-flex justify-content-between align-items-center mb-2">
<h4>Daftar Rating</h4>
<a href="?page=rating&action=create" class="btn btn-success btn-sm">Tambah</a>
</div>
<div class="table-responsive">
<table class="table table-bordered">
<thead><tr><th>#</th><th>Rating</th><th>Persentase Bonus</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($rows as $i=>$r): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($r['rating']) ?></td>
<td><?= number_format($r['persentase_bonus'],2) ?>%</td>
<td>
<a class="btn btn-sm btn-primary" href="?page=rating&action=edit&id=<?= $r['id_rating'] ?>">Edit</a>
<a class="btn btn-sm btn-danger" href="?page=rating&action=delete&id=<?= $r['id_rating'] ?>" onclick="return confirm('Hapus?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>