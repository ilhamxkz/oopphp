<?php
// app/views/sidebar.php
// Expect variable $activePage containing current page key (eg
// 'dashboard','karyawan',...)
$activePage = $activePage ?? '';
function active($p, $activePage) { return $p === $activePage ? 'bg-light
text-dark' : 'text-white'; }
?>
<html>
<nav class="bg-dark text-white sidebar p-3">
 <div class="d-flex align-items-center mb-3">
 <div class="brand text-white">Kantor</div>
 </div>
 <hr class="text-secondary">
 <ul class="nav flex-column">
 <li class="nav-item"><a class="nav-link <?= active('dashboard',
$activePage) ?>" href="?page=dashboard">Dashboard</a></li>
 <li class="nav-item"><a class="nav-link <?= active('karyawan',
$activePage) ?>" href="?page=karyawan">Daftar Karyawan</a></li>
 <li class="nav-item"><a class="nav-link <?= active('jabatan',
$activePage) ?>" href="?page=jabatan">Daftar Jabatan</a></li>
 <li class="nav-item"><a class="nav-link <?= active('rating',
$activePage) ?>" href="?page=rating">Daftar Rating</a></li>
 <li class="nav-item"><a class="nav-link <?= active('lembur',
$activePage) ?>" href="?page=lembur">Tarif Lembur</a></li>
 <li class="nav-item"><a class="nav-link <?= active('gaji',$activePage) ?>" href="?page=gaji">Gaji Karyawan</a></li>
 </ul>
</nav>
</html>