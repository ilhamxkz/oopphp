<?php
// app/models/Jabatan.php
require_once __DIR__ . '/../BaseModel.php';
class Jabatan extends BaseModel {
    protected $table = 'jabatan';

    public function create($data) {
        $sql = "INSERT INTO jabatan (jabatan, gaji_pokok, tunjangan) VALUES (:jabatan, :gaji_pokok, :tunjangan)";
        return $this->pdo->prepare($sql)->execute($data);
    }
    // public function update($id, $data) {
    //     $sql = "UPDATE jabatan SET jabatan=:jabatan, gaji_pokok=:gaji_pokok, tunjangan=:tunjangan WHERE id_jabatan=:id_jabatan";
    //     $data['id_jabatan'] = $id;
    //     return $this->pdo->prepare($sql)->execute($data);
    // }
}
