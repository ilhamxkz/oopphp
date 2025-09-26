<?php
// app/views/gaji_form.php
// Variables expected: $editing (optional), $karyawanList, $lemburList
$editing = $editing ?? null;
$actionUrl = $editing ? ('?page=gaji&action=edit&id='.$editing['id_gaji']) : '?page=gaji&action=create';
?>
<div class="card">
<div class="card-body">
<h5><?= $editing ? 'Edit Gaji Karyawan' : 'Hitung Gaji Karyawan' ?></h5>
<form method="post" action="<?= $actionUrl ?>">
<div class="mb-3">
<label class="form-label">Karyawan</label>
<select name="id_karyawan" class="form-select" required <?= $editing ? 'disabled' : '' ?>>
<option value="">-- Pilih Karyawan --</option>
<?php foreach($karyawanList as $k): ?>
<option value="<?= $k['id_karyawan'] ?>" 
        <?= (isset($editing['id_karyawan']) && $editing['id_karyawan']==$k['id_karyawan'])?'selected':'' ?>>
<?= htmlspecialchars($k['nama']) ?> - <?= htmlspecialchars($k['jabatan'] ?? 'No Position') ?>
</option>
<?php endforeach; ?>
</select>
<?php if($editing): ?>
<input type="hidden" name="id_karyawan" value="<?= $editing['id_karyawan'] ?>">
<?php endif; ?>
</div>

<div class="mb-3">
<label class="form-label">Periode</label>
<input type="month" name="periode" class="form-control" required 
       value="<?= $editing['periode'] ?? date('Y-m') ?>">
<small class="form-text text-muted">Pilih bulan dan tahun gaji</small>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Tarif Lembur (Opsional)</label>
<select name="id_lembur" class="form-select">
<option value="">-- Tidak Ada Lembur --</option>
<?php foreach($lemburList as $l): ?>
<option value="<?= $l['id_lembur'] ?>" 
        <?= (isset($editing['id_lembur']) && $editing['id_lembur']==$l['id_lembur'])?'selected':'' ?>>
Rp <?= number_format($l['tarif'],0,',','.') ?> per jam
</option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Lama Lembur (Jam)</label>
<input type="number" step="0.1" min="0" name="lama_lembur" class="form-control" 
       value="<?= $editing['lama_lembur'] ?? 0 ?>" placeholder="0">
<small class="form-text text-muted">Jumlah jam lembur dalam periode ini</small>
</div>
</div>

<div class="alert alert-info">
<strong>Info:</strong> Gaji akan dihitung otomatis berdasarkan:
<ul class="mb-0">
<li>Gaji pokok + tunjangan dari jabatan</li>
<li>Bonus berdasarkan rating karyawan</li>
<li>Total lembur = lama lembur × tarif lembur</li>
</ul>
</div>

<button class="btn btn-primary"><?= $editing ? 'Update Gaji' : 'Hitung & Simpan Gaji' ?></button>
<a href="?page=gaji" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>