<?php
// app/models/Rating.php
require_once __DIR__ . '/../BaseModel.php';
class Rating extends BaseModel {
    protected $table = 'rating';
    public function create($data) {
        $sql = "INSERT INTO rating (rating, persentase_bonus) VALUES (:rating, :persentase_bonus)";
        return $this->pdo->prepare($sql)->execute($data);
    }
    public function update($id, $data) {
        $sql = "UPDATE rating SET rating=:rating, persentase_bonus=:persentase_bonus WHERE id_rating=:id_rating";
        $data['id_rating'] = $id;
        return $this->pdo->prepare($sql)->execute($data);
    }
}
