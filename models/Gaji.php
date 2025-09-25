<?php
// app/models/Gaji.php
require_once __DIR__ . '/../BaseModel.php';
class Gaji extends BaseModel {
    protected $table = 'gaji';

    // Simpel: total_lembur = lama_lembur * tarif; total_bonus = persentase_bonus% dari gaji_pokok
    public function calculateAndCreate($data) {
        // ambil data karyawan, jabatan, rating, lembur
        $kStmt = $this->pdo->prepare("SELECT k.*, j.gaji_pokok, j.tunjangan, r.persentase_bonus
            FROM karyawan k
            LEFT JOIN jabatan j ON k.id_jabatan=j.id_jabatan
            LEFT JOIN rating r ON k.id_rating=r.id_rating
            WHERE k.id_karyawan=:id_karyawan LIMIT 1");
        $kStmt->execute(['id_karyawan'=>$data['id_karyawan']]);
        $k = $kStmt->fetch(PDO::FETCH_ASSOC);
        $tarif = 0;
        if (!empty($data['id_lembur'])) {
            $lStmt = $this->pdo->prepare("SELECT tarif FROM lembur WHERE id_lembur=:id LIMIT 1");
            $lStmt->execute(['id'=>$data['id_lembur']]);
            $l = $lStmt->fetch(PDO::FETCH_ASSOC);
            $tarif = $l['tarif'] ?? 0;
        }

        $lama = (float)($data['lama_lembur'] ?? 0);
        $total_lembur = $lama * (float)$tarif;
        $gaji_pokok = (float)($k['gaji_pokok'] ?? 0);
        $tunjangan = (float)($k['tunjangan'] ?? 0);
        $persentase = (float)($k['persentase_bonus'] ?? 0);
        $total_bonus = ($persentase/100) * $gaji_pokok;
        $total_pendapatan = $gaji_pokok + $tunjangan + $total_lembur + $total_bonus;

        $sql = "INSERT INTO gaji (id_karyawan, id_lembur, periode, lama_lembur, total_lembur, total_bonus, total_tunjangan, total_pendapatan)
                VALUES (:id_karyawan, :id_lembur, :periode, :lama_lembur, :total_lembur, :total_bonus, :total_tunjangan, :total_pendapatan)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_karyawan'=>$data['id_karyawan'],
            'id_lembur'=>$data['id_lembur'] ?: null,
            'periode'=>$data['periode'],
            'lama_lembur'=>$lama,
            'total_lembur'=>$total_lembur,
            'total_bonus'=>$total_bonus,
            'total_tunjangan'=>$tunjangan,
            'total_pendapatan'=>$total_pendapatan
        ]);
    }

    public function allWithRelations() {
        $sql = "SELECT g.*, k.nama, j.jabatan FROM gaji g
                LEFT JOIN karyawan k ON g.id_karyawan = k.id_karyawan
                LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan
                ORDER BY g.created_at DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
