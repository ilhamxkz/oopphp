<?php
// app/views/rating_form.php
// Variables expected: $editing (optional)
$editing = $editing ?? null;
$actionUrl = $editing ? ('?page=rating&action=edit&id='.$editing['id_rating']) : '?page=rating&action=create';
?>
<div class="card">
<div class="card-body">
<h5><?= $editing ? 'Edit Rating' : 'Tambah Rating' ?></h5>
<form method="post" action="<?= $actionUrl ?>">
<div class="mb-3">
<label class="form-label">Rating</label>
<input required name="rating" class="form-control" value="<?= $editing['rating'] ?? '' ?>" placeholder="Contoh: Excellent, Good, Average">
</div>
<div class="mb-3">
<label class="form-label">Persentase Bonus (%)</label>
<input type="number" step="0.01" min="0" max="100" name="persentase_bonus" class="form-control" value="<?= $editing['persentase_bonus'] ?? 0 ?>" placeholder="0-100">
<small class="form-text text-muted">Masukkan persentase bonus (0-100%)</small>
</div>
<button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
<a href="?page=rating" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>