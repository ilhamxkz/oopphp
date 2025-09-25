<?php
// app/views/lembur_form.php
// Variables expected: $editing (optional)
$editing = $editing ?? null;
$actionUrl = $editing ? ('?page=lembur&action=edit&id='.$editing['id_lembur']) : '?page=lembur&action=create';
?>
<div class="card">
<div class="card-body">
<h5><?= $editing ? 'Edit Tarif Lembur' : 'Tambah Tarif Lembur' ?></h5>
<form method="post" action="<?= $actionUrl ?>">
<div class="mb-3">
<label class="form-label">Tarif per Jam</label>
<div class="input-group">
<span class="input-group-text">Rp</span>
<input type="number" step="0.01" min="0" required name="tarif" class="form-control" value="<?= $editing['tarif'] ?? '' ?>" placeholder="50000">
</div>
<small class="form-text text-muted">Masukkan tarif lembur per jam dalam rupiah</small>
</div>
<button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
<a href="?page=lembur" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>