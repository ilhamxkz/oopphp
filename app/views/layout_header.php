<?php
// app/views/layout_header.php
// Requires: $title (optional)
$title = $title ?? 'Admin Kantor';
?>
<!doctype html>
<html lang="id">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <title><?= htmlspecialchars($title) ?></title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/
bootstrap.min.css" rel="stylesheet">
 <style>
 body { min-height:100vh; }
 .sidebar { min-width:240px; max-width:260px; }
 @media (max-width: 768px) { .sidebar { position: fixed; z-index: 1030;
left:-300px; transition: left .25s;} .sidebar.show { left:0; } }
 .content { padding:20px; }
 .brand { font-weight:700; }
 </style>
</head>
<body>
<div class="d-flex">
<?php // sidebar will be included by caller ?>
<main class="flex-fill content">
