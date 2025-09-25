<?php
// app/models/Karyawan.php
require_once __DIR__ . '/../BaseModel.php';
class Karyawan extends BaseModel {
    protected $table = 'karyawan';

    public function create($data) {
        $sql = "INSERT INTO karyawan (id_jabatan, id_rating, nama, divisi, alamat, umur, jenis_kelamin, status)
                VALUES (:id_jabatan, :id_rating, :nama, :divisi, :alamat, :umur, :jenis_kelamin, :status)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE karyawan SET id_jabatan=:id_jabatan, id_rating=:id_rating, nama=:nama, divisi=:divisi, alamat=:alamat, umur=:umur, jenis_kelamin=:jenis_kelamin, status=:status
                WHERE id_karyawan=:id_karyawan";
        $data['id_karyawan'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function allWithRelations($limit = null) {
        $sql = "SELECT k.*, j.jabatan, j.gaji_pokok, j.tunjangan, r.rating, r.persentase_bonus
                FROM karyawan k
                LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan
                LEFT JOIN rating r ON k.id_rating = r.id_rating
                ORDER BY k.created_at DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
