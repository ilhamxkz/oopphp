<?php
// app/models/Lembur.php
require_once __DIR__ . '/../BaseModel.php';
class Lembur extends BaseModel {
    protected $table = 'lembur';
    public function create($data) {
        $sql = "INSERT INTO lembur (tarif) VALUES (:tarif)";
        return $this->pdo->prepare($sql)->execute($data);
    }
    public function update($id, $data) {
        $sql = "UPDATE lembur SET tarif=:tarif WHERE id_lembur=:id_lembur";
        $data['id_lembur'] = $id;
        return $this->pdo->prepare($sql)->execute($data);
    }
}
