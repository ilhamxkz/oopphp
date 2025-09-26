<?php
// app/BaseModel.php
class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function all($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        if ($limit) $sql .= " LIMIT $limit";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id, $pk = 'id') {
        $sql = "SELECT * FROM {$this->table} WHERE $pk = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id, $pk = 'id') {
        $sql = "DELETE FROM {$this->table} WHERE $pk = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }



    public function create(array $data) {
        if (empty($data)) return false;
        $cols = array_keys($data);
        $place = array_map(function($c){return ':' . $c;}, $cols);
        $sql = "INSERT INTO `{$this->table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $place) . ")";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, array $data, $pk = null) {
        $pk = $pk ?? $this->pk;
        if (empty($data)) return false;
        $sets = [];
        foreach ($data as $k => $v) {
            $sets[] = "`$k` = :$k";
        }
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $sets) . " WHERE `{$pk}` = :_id";
        $params = $data;
        $params['_id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

}

// ---------------------------
// Domain models
// ---------------------------
class Karyawan extends BaseModel {
    public function __construct(PDO $pdo){ parent::__construct($pdo, 'karyawan', 'id_karyawan'); }

    // join jabatan + rating
    public function allWithRelations($limit = null) {
        $sql = "SELECT k.*, j.jabatan, j.gaji_pokok, j.tunjangan, r.rating, r.persentase_bonus
                FROM karyawan k
                LEFT JOIN jabatan j ON j.id_jabatan = k.id_jabatan
                LEFT JOIN rating r ON r.id_rating = k.id_rating
                ORDER BY k.created_at DESC";
        if ($limit) $sql .= " LIMIT " . intval($limit);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}

class Jabatan extends BaseModel { public function __construct(PDO $pdo){ parent::__construct($pdo, 'jabatan', 'id_jabatan'); } }
class Rating extends BaseModel { public function __construct(PDO $pdo){ parent::__construct($pdo, 'rating', 'id_rating'); } }
class Lembur extends BaseModel { public function __construct(PDO $pdo){ parent::__construct($pdo, 'lembur', 'id_lembur'); } }

class Gaji extends BaseModel {
    public function __construct(PDO $pdo){ parent::__construct($pdo, 'gaji', 'id_gaji'); }

    public function allWithRelations() {
        $sql = "SELECT g.*, k.nama, j.jabatan, r.rating
                FROM gaji g
                LEFT JOIN karyawan k ON k.id_karyawan = g.id_karyawan
                LEFT JOIN jabatan j ON j.id_jabatan = k.id_jabatan
                LEFT JOIN rating r ON r.id_rating = k.id_rating
                ORDER BY g.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // helper untuk menghitung ulang dan update
    public function calculateAndUpdate($id, $input) {
        $existing = $this->find($id, 'id_gaji');
        if (!$existing) return false;

        $id_karyawan = $input['id_karyawan'] ?? $existing['id_karyawan'];
        $id_lembur = (!empty($input['id_lembur'])) ? $input['id_lembur'] : $existing['id_lembur'];
        $periode = $input['periode'] ?? $existing['periode'];
        $lama_lembur = floatval($input['lama_lembur'] ?? $existing['lama_lembur'] ?? 0);

        // ambil data relasi
        $stmt = $this->pdo->prepare("SELECT k.*, j.gaji_pokok, j.tunjangan, r.persentase_bonus, l.tarif as tarif_lembur
                                     FROM karyawan k
                                     LEFT JOIN jabatan j ON j.id_jabatan = k.id_jabatan
                                     LEFT JOIN rating r ON r.id_rating = k.id_rating
                                     LEFT JOIN lembur l ON l.id_lembur = :id_lembur
                                     WHERE k.id_karyawan = :id_karyawan LIMIT 1");
        $stmt->execute(['id_lembur'=>$id_lembur, 'id_karyawan'=>$id_karyawan]);
        $row = $stmt->fetch();
        $gaji_pokok = $row['gaji_pokok'] ?? 0;
        $tunjangan = $row['tunjangan'] ?? 0;
        $persentase_bonus = $row['persentase_bonus'] ?? 0;
        $tarif_lembur = $row['tarif_lembur'] ?? 0;

        $bonus = ($gaji_pokok + $tunjangan) * ($persentase_bonus / 100.0);
        $total_lembur = $tarif_lembur * $lama_lembur;
        $total_pendapatan = $gaji_pokok + $tunjangan + $bonus + $total_lembur;

        $payload = [
            'id_karyawan'=>$id_karyawan,
            'periode'=>$periode,
            'gaji_pokok'=>$gaji_pokok,
            'tunjangan'=>$tunjangan,
            'persentase_bonus'=>$persentase_bonus,
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

// ---------------------------
// instantiate
// ---------------------------
$karyawanModel = new Karyawan($pdo);
$jabatanModel = new Jabatan($pdo);
$ratingModel = new Rating($pdo);
$lemburModel = new Lembur($pdo);
$gajiModel = new Gaji($pdo);

// ---------------------------
// router: same structure as original, but with some safe checks
// (omitted here in the bundle to keep focus on model fixes; you can copy your original
// route-handling block and replace model instantiation with the above.)

// ---------------------------
// Saran migration / foreign keys (jalankan di mysql):
// 1) buat foreign key yang aman agar saat menghapus jabatan/rating tidak error:
// ALTER TABLE karyawan
//   ADD CONSTRAINT fk_karyawan_jabatan FOREIGN KEY (id_jabatan) REFERENCES jabatan(id_jabatan) ON DELETE SET NULL ON UPDATE CASCADE,
//   ADD CONSTRAINT fk_karyawan_rating FOREIGN KEY (id_rating) REFERENCES rating(id_rating) ON DELETE SET NULL ON UPDATE CASCADE;
//
// 2) untuk gaji: jika ingin otomatis hapus gaji saat karyawan dihapus (opsional):
// ALTER TABLE gaji
//   ADD CONSTRAINT fk_gaji_karyawan FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan) ON DELETE CASCADE ON UPDATE CASCADE;
//
// Jika Anda memilih ON DELETE SET NULL sesuaikan kolom id_karyawan pada gaji agar nullable.

// ---------------------------
// Notes:
// - Perbaikan utama: implementasi model generik + allWithRelations di Karyawan & Gaji
// - Pastikan struktur kolom pada DB sesuai dengan field yang dipakai (gaji_pokok vs total_tunjangan dll).
// - Jika sebelumnya anda menyimpan nama kolom yang berbeda, sesuaikan property dan queries pada model Gaji.
// - Ubah metode delete pada UI agar menggunakan FORM POST (lebih aman) dan hindari delete via GET.

// selesai. Split file ke app/models/*.php jika mau.
    
?>