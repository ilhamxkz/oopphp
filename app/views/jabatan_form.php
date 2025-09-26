<?php
// app/views/jabatan_form.php
// Variables expected: $editing (optional)
$editing = $editing ?? null;
$actionUrl = $editing ? ('?page=jabatan&action=edit&id='.$editing['id_jabatan']) : '?page=jabatan&action=create';
?>
<div class="card">
<div class="card-body">
<h5><?= $editing ? 'Edit Jabatan' : 'Tambah Jabatan' ?></h5>
<form method="post" action="<?= $actionUrl ?>">
<div class="mb-3">
<label class="form-label">Jabatan</label>
<input required name="jabatan" class="form-control" value="<?= $editing['jabatan'] ?? '' ?>">
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Gaji Pokok</label>
<input type="number" step="0.01" name="gaji_pokok" class="form-control" value="<?= $editing['gaji_pokok'] ?? 0 ?>">
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Tunjangan</label>
<input type="number" step="0.01" name="tunjangan" class="form-control" value="<?= $editing['tunjangan'] ?? 0 ?>">
</div>
</div>
<button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
<a href="?page=jabatan" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>