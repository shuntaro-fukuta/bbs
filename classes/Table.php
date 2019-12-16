<?php

require_once(__DIR__ . '/../functions/general.php');
require_once(__DIR__ . '/../database/db_connect.php');

abstract class Table
{
    protected $mysqli;
    protected $table_name;
    protected $bind_types;

    public function __construct()
    {
        $this->mysqli = connect_mysqli();
    }

    public function selectRecord(array $columns, array $where)
    {
        $options = [];

        $options['where'] = $where;
        $options['limit'] = 1;

        $records = $this->selectRecords($columns, $options);

        if ($records === []) {
            return null;
        }

        return $records[0];
    }

    public function selectRecords(array $columns, array $options = null)
    {
        $columns = implode(',', $columns);
        $query   = "SELECT {$columns} FROM {$this->table_name}";

        if (isset($options)) {
            if (isset($options['where'])) {
                $where_bind_items = $this->getWhereBindItems($options['where']);

                $query        .= $where_bind_items['query'];
                $where_columns = $where_bind_items['columns'];
                $where_values  = $where_bind_items['values'];
            }

            if (isset($options['order_by'])) {
                $query .= " ORDER BY {$options['order_by']}";
            }

            if (isset($options['limit'])) {
                $query .= ' LIMIT ' . (int) $options['limit'];

                if (isset($options['offset'])) {
                    $query .= ' OFFSET ' . (int) $options['offset'];
                }
            }
        }

        $stmt = $this->prepareStatement($query);

        if (isset($options) && isset($options['where'])) {
            $stmt = $this->bindParams($stmt, $where_columns, $where_values);
        }

        $stmt->execute();

        if (!($results = $stmt->get_result())) {
            throw new RuntimeException('Failed to select records from table.');
        }

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public function count(array $where = null)
    {
        $query = "SELECT COUNT(*) FROM {$this->table_name}";

        if (!is_null($where)) {
            $where_bind_items = $this->getWhereBindItems($where);

            $query        .= $where_bind_items['query'];
            $where_columns = $where_bind_items['columns'];
            $where_values  = $where_bind_items['values'];

            $stmt = $this->prepareStatement($query);
            $stmt = $this->bindParams($stmt, $where_columns, $where_values);

            if (!$stmt->execute()) {
                throw new LogicException('Failed to execute statement.');
            }

            $results = $stmt->get_result();
        } else {
            $results = $this->mysqli->query($query);
        }

        if ($results === false) {
            throw new RuntimeException('Failed to select count from table.');
        }

        $count = (int) $results->fetch_assoc()['COUNT(*)'];

        return $count;
    }

    public function insert(array $column_values)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $insert_columns = implode(', ', $columns);

        $place_holders = array_fill(0, count($values), '?');
        $place_holders = implode(', ', $place_holders);

        $query = "INSERT INTO {$this->table_name} ({$insert_columns}) VALUES ({$place_holders})";

        $stmt = $this->prepareStatement($query);
        $stmt = $this->bindParams($stmt, $columns, $values);

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to insert records into table.');
        }
    }

    public function update(array $column_values, array $where = null)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $update_columns = [];
        foreach ($columns as $column) {
            $update_columns[] = "{$column} = ?";
        }
        $update_columns = implode(', ', $update_columns);

        $query = "UPDATE {$this->table_name} SET {$update_columns}";

        if (!is_null($where)) {
            $where_bind_items = $this->getWhereBindItems($where);

            $query  .= $where_bind_items['query'];
            $columns = array_merge($columns, $where_bind_items['columns']);
            $values  = array_merge($values, $where_bind_items['values']);
        }

        $stmt = $this->prepareStatement($query);
        $stmt = $this->bindParams($stmt, $columns, $values);

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to update records.');
        }
    }

    public function delete(array $where = null)
    {
        $query = "DELETE FROM {$this->table_name}";

        if (is_null($where)) {
            $stmt = $this->prepareStatement($query);
        } else {
            $where_bind_items = $this->getWhereBindItems($where);

            $query  .= $where_bind_items['query'];
            $columns = $where_bind_items['columns'];
            $values  = $where_bind_items['values'];

            $stmt = $this->prepareStatement($query);
            $stmt = $this->bindParams($stmt, $columns, $values);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to delete records.');
        }
    }

    protected function bindParams(mysqli_stmt $stmt, array $columns, array $values)
    {
        $types = '';
        foreach ($columns as $column) {
            if (!isset($this->bind_types[$column])) {
                throw new InvalidArgumentException("{$column} column doesn't exist.");
            }

            $types .= $this->bind_types[$column];
        }

        if (!$stmt->bind_param($types, ...$values)) {
            throw new LogicException('Failed to bind params to statement.');
        }

        return $stmt;
    }

    protected function prepareStatement(string $query)
    {
        if (!($stmt = $this->mysqli->prepare($query))) {
            throw new LogicException('Failed to prepare statement.');
        }

        return $stmt;
    }

    protected function getWhereBindItems(array $wheres)
    {
        if ($wheres === []) {
            throw new InvalidArgumentException('Where condition is required.');
        }

        $queries = [];
        $columns = [];
        $values  = [];

        foreach ($wheres as $where) {
            if (!is_array($where)) {
                throw new InvalidArgumentException("Where's condition format must be array.");
            }

            $column = $where[0] ?? null;
            if (!is_string($column)) {
                throw new InvalidArgumentException("Where's first parameter must be string.");
            }

            $operator = $where[1] ?? null;
            if (!in_array($operator, ['<', '>', '=', '!=', '<=', '>=', '<>'])) {
                throw new InvalidArgumentException("Where's second parameter must be one of these [<, >, =, !=, <=, >=, <>].");
            }

            $value = $where[2] ?? null;
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException("Where's third parameter must be string or number.");
            }

            $queries[] = "{$column} {$operator} ?";
            $columns[] = $column;
            $values[]  = $value;
        }

        $where_bind_items = [];

        $where_bind_items['query']   = ' WHERE ' . implode(' AND ', $queries);
        $where_bind_items['columns'] = $columns;
        $where_bind_items['values']  = $values;

        return $where_bind_items;
    }
}