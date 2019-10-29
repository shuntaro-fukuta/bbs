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
        $columns = implode(',', array_keys($column_values));

        $place_holders = array_fill(0, count($column_values), '?');
        $place_holders = implode(',', $place_holders);

        $query = "INSERT INTO {$this->table_name} ({$columns}) VALUES ({$place_holders})";

        $stmt = $this->getParamBindedStatement($query, $column_values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to insert records into table.');
        }
    }

    public function update(string $where, array $column_values)
    {
        $query = "UPDATE {$this->table_name} SET ";

        foreach (array_keys($column_values) as $column) {
            $query .= "{$column} = ?, ";
        }
        $query = rtrim($query, ', ');

        $query .= " WHERE {$where}";

        $stmt = $this->getParamBindedStatement($query, $column_values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to update records.');
        }
    }

    // $wheres = ['where' => ['id', '=', $id], 'AND' => ['created_at', '>', '2019'], ...];
    public function delete(array $wheres = null)
    {
        $query = "DELETE FROM {$this->table_name}";

        if (isset($wheres)) {
            $query .= ' WHERE';

            // 変数名考える
            $where_values = [];
            foreach ($wheres as $key => $where) {
                $column   = $where[0];
                $operator = $where[1];
                $value    = $where[2];

                if ($key === 'AND') {
                    $query .= ' AND';
                }
                if ($key === 'OR') {
                    $query .= ' OR';
                }

                $query .= " {$column} {$operator} ?";
                $where_values[$column] = $value;
            }
        }

        $stmt = $this->getParamBindedStatement($query, $where_values);


        if (!$stmt->execute()) {
            throw new LogicException('Failed to delete records.');
        }
    }

    private function getParamBindedStatement(string $query, array $column_values)
    {
        $types  = '';
        $values = [];

        foreach ($column_values as $column => $value) {
            $types   .= $this->column_types[$column];
            $values[] = $value;
        }

        $stmt = $this->db_instance->prepare($query);
        $stmt->bind_param($types, ...$values);

        return $stmt;
    }
}