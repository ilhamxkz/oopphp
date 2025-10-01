<?php
// app/models/Gaji.php
require_once __DIR__ . '/../BaseModel.php';
class Gaji extends BaseModel {
    protected $table = 'gaji';
    protected $pk = 'id_gaji';

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

    // recalc and update an existing gaji row
    public function calculateAndUpdate($id, $input) {
    $existing = $this->find($id, 'id_gaji');
    if (!$existing) return false;

    $id_karyawan = $input['id_karyawan'] ?? $existing['id_karyawan'];
    $id_lembur = (!empty($input['id_lembur'])) ? $input['id_lembur'] : $existing['id_lembur'];
    $periode = $input['periode'] ?? $existing['periode'];
    $lama_lembur = floatval($input['lama_lembur'] ?? $existing['lama_lembur'] ?? 0);

    $stmt = $this->pdo->prepare("SELECT k.*, j.gaji_pokok, j.tunjangan, r.persentase_bonus, l.tarif as tarif_lembur
                     FROM karyawan k
                     LEFT JOIN jabatan j ON j.id_jabatan = k.id_jabatan
                     LEFT JOIN rating r ON r.id_rating = k.id_rating
                     LEFT JOIN lembur l ON l.id_lembur = :id_lembur
                     WHERE k.id_karyawan = :id_karyawan LIMIT 1");
    $stmt->execute(['id_lembur'=>$id_lembur, 'id_karyawan'=>$id_karyawan]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $gaji_pokok = $row['gaji_pokok'] ?? 0;
    $tunjangan = $row['tunjangan'] ?? 0;
    $persentase = $row['persentase_bonus'] ?? 0;
    $tarif_lembur = $row['tarif_lembur'] ?? 0;

    $bonus = ($gaji_pokok + $tunjangan) * ($persentase / 100.0);
    $total_lembur = $tarif_lembur * $lama_lembur;
    $total_pendapatan = $gaji_pokok + $tunjangan + $bonus + $total_lembur;

    $payload = [
        'id_karyawan'=>$id_karyawan,
        'periode'=>$periode,
        'gaji_pokok'=>$gaji_pokok,
        'tunjangan'=>$tunjangan,
        'persentase_bonus'=>$persentase,
        'bonus'=>$bonus,
        'id_lembur'=>$id_lembur,
        'lama_lembur'=>$lama_lembur,
        'total_lembur'=>$total_lembur,
        'total_pendapatan'=>$total_pendapatan,
        'updated_at'=>date('Y-m-d H:i:s')
    ];

    return $this->update($id, $payload, 'id_gaji');
    }
}
