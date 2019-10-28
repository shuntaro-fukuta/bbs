<?php

require_once('functions.php');

class DatabaseOperator
{
    private $db_instance;
    private $table_name   = 'posts';
    private $column_types = [
        'title'    => 's',
        'comment'  => 's',
        'password' => 's',
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
        // ------- prepare型 ---------
        $columns = implode(',', array_keys($column_values));

        $place_holders = array_fill(0, count($column_values), '?');
        $place_holders = implode(',', $place_holders);

        $query = "INSERT INTO {$this->table_name} ({$columns}) VALUES ({$place_holders})";

        $stmt = $this->getParamBindedStatement($query, $column_values);

        if (!$stmt->execute()) {
            throw new Exception('Failed to insert records into table.');
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
            throw new Exception('Failed to update records.');
        }
    }

    public function delete(string $where)
    {
        $query = "DELETE FROM {$this->table_name} WHERE {$where}";

        $this->db_instance->query($query);
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