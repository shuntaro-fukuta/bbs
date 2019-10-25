<?php

class DatabaseOperator
{
    private $db_instance;
    private $table_name = 'posts';

    public function __construct($db_instance)
    {
        $this->db_instance = $db_instance;
    }

    public function setTableName(string $name)
    {
        $this->table_name = $name;
    }

    public function select(array $parameters)
    {
        $query = "SELECT {$parameters['column_name']} FROM {$this->table_name}";

        if (isset($parameters['where'])){
            $query .= " WHERE {$parameters['where']}";
        }
        if (isset($parameters['order_by'])){
            $query .= " ORDER BY {$parameters['order_by']}";
        }
        if (isset($parameters['limit'])){
            $query .= " LIMIT {$parameters['limit']}";

            if (isset($parameters['offset'])){
                $query .= " OFFSET {$parameters['offset']}";
            }
        }

        return $this->db_instance->query($query);
    }

    public function insert(array $parameters)
    {
        $query = "INSERT INTO {$this->table_name} (";

        foreach (array_keys($parameters) as $column_name) {
            $query .= "{$column_name},";
        }

        $query = substr_replace($query, ')', -1, 1);

        $query .= " VALUES (";

        foreach ($parameters as $value) {
            $value  = $this->db_instance->real_escape_string($value);
            $query .= "'{$value}',";
        }

        $query = substr_replace($query, ')', -1, 1);

        $this->db_instance->query($query);
    }

    public function update(string $where, array $parameters)
    {
        $query = "UPDATE {$this->table_name} SET ";

        foreach ($parameters as $column_name => $value) {
            $value  = $this->db_instance->real_escape_string($value);
            $query .= "{$column_name}='{$value}',";
        }

        $query = rtrim($query, ',');

        $query .= " WHERE {$where}";

        $this->db_instance->query($query);
    }

    public function delete(string $where)
    {
        $query = "DELETE FROM {$this->table_name} WHERE {$where}";

        $this->db_instance->query($query);
    }
}