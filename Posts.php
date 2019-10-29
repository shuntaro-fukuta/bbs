<?php

require_once('functions.php');

class Posts
{
    private $db_instance;
    private $table_name   = 'posts';
    private $column_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'password'   => 's',
        'created_at' => 's',
    ];

    public function __construct($db_instance)
    {
        $this->db_instance = $db_instance;
    }

    public function setTableName(string $name)
    {
        $this->table_name = $name;
    }

    public function select(string $column, array $options = null)
    {
        $query = "SELECT {$column} FROM {$this->table_name}";

        if (isset($options)) {
            if (isset($options['where'])){
                $query .= " WHERE {$options['where']}";
            }
            if (isset($options['order_by'])){
                $query .= " ORDER BY {$options['order_by']}";
            }
            if (isset($options['limit'])){
                $query .= " LIMIT {$options['limit']}";

                if (isset($options['offset'])){
                    $query .= " OFFSET {$options['offset']}";
                }
            }
        }

        // fetchまでやりたい
        return $this->db_instance->query($query);
    }

    public function insert(array $column_values)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $query_columns = implode(', ', $columns);

        $place_holders = array_fill(0, count($values), '?');
        $place_holders = implode(', ', $place_holders);

        $query = "INSERT INTO {$this->table_name} ({$query_columns}) VALUES ({$place_holders})";

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to insert records into table.');
        }
    }

    // whereを配列で受け取る
    public function update(string $where, array $column_values)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $column_with_placeholders = [];
        foreach ($columns as $column) {
            $column_with_placeholders[] = "{$column} = ?";
        }

        $column_with_placeholders = implode(', ', $column_with_placeholders);

        $query = "UPDATE {$this->table_name} SET {$column_with_placeholders}";

        // whereのqueryを取得するメソッドをつくる
        $query .= " WHERE {$where}";

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to update records.');
        }
    }

    // $wheres = ['where' => ['id', '=', $id], 'and' => ['created_at', '>', '2019'], ...];
    public function delete(array $wheres = null)
    {
        $query = "DELETE FROM {$this->table_name}";

        if (!isset($wheres)) {
            $this->db_instance->query($query);
        }

        $columns = [];
        $values  = [];

        foreach ($wheres as $key => $where) {
            $column   = $where[0];
            $operator = $where[1];
            $value    = $where[2];

            // 大文字小文字どっちも対応？
            if ($key === 'where') {
                $query .= ' WHERE ';
            } elseif ($key === 'and') {
                $query .= ' AND ';
            } elseif ($key === 'or') {
                $query .= ' OR ';
            }
            $query .= "{$column} {$operator} ?";

            $columns[] = $column;
            $values[]  = $value;
        }

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to delete records.');
        }
    }

    private function getParamBindedStatement(string $query, array $columns, array $values)
    {
        $types  = '';
        foreach ($columns as $column) {
            $types .= $this->column_types[$column];
        }
        $stmt = $this->db_instance->prepare($query);
        $stmt->bind_param($types, ...$values);

        return $stmt;
    }
}