<?php
?>
<div class="card">
<div class="card-body">
<h5><?= $editing ? "Edit Karyawan" : "Tambah Karyawan" ?></h5>
<form method="post" action="<?= $actionUrl ?>">
<div class="mb-3">
<label class="form-label">Nama</label>
<input required name="nama" class="form-control" value="<?= $editing['nama'] ?? '' ?>">
</div>
<div class="mb-3">
<label class="form-label">Divisi</label>
<input name="divisi" class="form-control" value="<?= $editing['divisi'] ?? '' ?>">
</div>
<div class="mb-3">
<label class="form-label">Jabatan</label>
<select name="id_jabatan" class="form-select">
<option value="">-- Pilih --</option>
<?php foreach($jabatanList as $j): ?>
<option value="<?= $j['id_jabatan'] ?>" <?= (isset($editing['id_jabatan']) && $editing['id_jabatan']==$j['id_jabatan'])?'selected':'' ?>>
<?= htmlspecialchars($j['jabatan']) ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="mb-3">
<label class="form-label">Rating</label>
<select name="id_rating" class="form-select">
<option value="">-- Pilih --</option>
<?php foreach($ratingList as $r): ?>
<option value="<?= $r['id_rating'] ?>" <?= (isset($editing['id_rating']) && $editing['id_rating']==$r['id_rating'])?'selected':'' ?>>
<?= htmlspecialchars($r['rating']) ?>
</option>
<?php endforeach; ?>
</select>
</div>


<div class="row">
<div class="col-md-4 mb-3">
<label class="form-label">Umur</label>
<input type="number" name="umur" class="form-control" value="<?= $editing['umur'] ?? '' ?>">
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-select">
<option value="Laki-laki" <?= (isset($editing['jenis_kelamin']) && $editing['jenis_kelamin']=='Laki-laki')?'selected':'' ?>>Laki-laki</option>
<option value="Perempuan" <?= (isset($editing['jenis_kelamin']) && $editing['jenis_kelamin']=='Perempuan')?'selected':'' ?>>Perempuan</option>
</select>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">Status</label>
<input name="status" class="form-control" value="<?= $editing['status'] ?? '' ?>">
</div>
</div>


<div class="mb-3">
<label class="form-label">Alamat</label>
<textarea name="alamat" class="form-control"><?= $editing['alamat'] ?? '' ?></textarea>
</div>


<button class="btn btn-primary"><?= $editing ? 'Update' : 'Simpan' ?></button>
<a href="?page=karyawan" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>