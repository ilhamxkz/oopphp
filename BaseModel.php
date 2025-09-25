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
}
