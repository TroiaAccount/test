<?php
class Model extends Helpers
{
    protected $table;
    private $where_clause;

    public function __construct()
    {
        parent::__construct();
    }

    protected function setTable($table){
        $this->table = $table;
    }

    protected function insert(array $parameters)
    {
        $columns = implode(',', array_keys($parameters));
        $values = "'" . implode("','", array_values($parameters)) . "'";
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->table, $columns, $values);
        $this->conn->query($sql);
        return $this->conn->insert_id;
    }

    protected function get()
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s", $this->table, $this->where_clause);
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    protected function first()
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s LIMIT 1", $this->table, $this->where_clause);
        return $this->conn->query($sql)->fetch_assoc();
    }

    protected function delete()
    {
        $sql = sprintf("DELETE FROM %s WHERE %s", $this->table, $this->where_clause);
        $this->conn->query($sql);
        return $this;
    }

    protected function update(array $parameters)
    {
        $updates = [];
        foreach ($parameters as $column => $value) {
            $updates[] = sprintf("%s = '%s'", $column, $value);
        }
        $sql = sprintf("UPDATE %s SET %s WHERE %s", $this->table, implode(',', $updates), $this->where_clause);
        $this->conn->query($sql);
        return $this;
    }

    protected function where($conditions)
    {
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = sprintf("%s = '%s'", $column, $value);
        }
        $this->where_clause = implode(' AND ', $where);
        return $this;
    }

    protected function random()
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s ORDER BY RAND() LIMIT 1", $this->table, $this->where_clause);
        return $this->conn->query($sql)->fetch_assoc();
    }
}
