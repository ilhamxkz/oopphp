<?php
// app/BaseModel.php
class BaseModel {
    protected $pdo;
    protected $table;
    protected $pk = 'id';

    public function __construct(PDO $pdo, $table = null, $pk = null) {
        $this->pdo = $pdo;
        // only override if explicit values passed; keep child class defaults otherwise
        if ($table !== null) $this->table = $table;
        if ($pk !== null) $this->pk = $pk;
    }

    public function all($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit) $sql .= " LIMIT " . intval($limit);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id, $pk = null) {
        $pk = $pk ?? $this->pk;
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$pk}` = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id, $pk = null) {
        $pk = $pk ?? $this->pk;
        $sql = "DELETE FROM `{$this->table}` WHERE `{$pk}` = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function create(array $data) {
        if (empty($data)) return false;
        $cols = array_keys($data);
        $place = array_map(function($c){ return ':' . $c; }, $cols);
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

?>