<?php

class Storage_Database_MySQL extends Storage_Database
{
    private $bind_types = [
        'string'  => 's',
        'integer' => 'i',
        'double'  => 'd',
        'NULL'    => 's',
    ];

    public function __construct($config = [])
    {
        if (!isset($config['charset'])) {
            $config['charset'] = 'utf8';
        }

        parent::__construct($config);
    }

    public function selectRecord(string $table_name, array $columns, array $where)
    {
        $options = [];

        $options['where'] = $where;
        $options['limit'] = 1;

        $records = $this->selectRecords($table_name, $columns, $options);

        if ($records === []) {
            return null;
        }

        return $records[0];
    }

    public function selectRecords(string $table_name, array $columns, array $options = null)
    {
        $columns = implode(',', $columns);
        $query   = "SELECT {$columns} FROM {$table_name}";

        if (isset($options)) {
            if (isset($options['where'])) {
                $where = $options['where'];

                // TODO: $where チェック
                $query       .= ' WHERE ' . $where['condition'];
                $where_values = $where['values'];
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

        if (!empty($options) && isset($options['where'])) {
            $stmt = $this->bindParams($stmt, $where_values);
        }

        $stmt->execute();

        if (!($results = $stmt->get_result())) {
            throw new RuntimeException('Failed to select records from table.');
        }

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public function count(string $table_name, array $where = null)
    {
        $query = "SELECT COUNT(*) FROM {$table_name}";

        if (!empty($where)) {
            // TODO: whereチェック
            $query       .= ' WHERE ' . $where['condition'];
            $where_values = $where['values'];

            $stmt = $this->prepareStatement($query);
            $stmt = $this->bindParams($stmt, $where_values);

            if (!$stmt->execute()) {
                throw new LogicException('Failed to execute statement.');
            }

            $results = $stmt->get_result();
        } else {
            $results = $this->conn->query($query);
        }

        if ($results === false) {
            throw new RuntimeException('Failed to select count from table.');
        }

        $count = (int) $results->fetch_assoc()['COUNT(*)'];

        return $count;
    }

    public function insert(string $table_name, array $column_values)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $insert_columns = implode(', ', $columns);

        $place_holders = array_fill(0, count($values), '?');
        $place_holders = implode(', ', $place_holders);

        $query = "INSERT INTO {$table_name} ({$insert_columns}) VALUES ({$place_holders})";

        $stmt = $this->prepareStatement($query);
        $stmt = $this->bindParams($stmt, $values);

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to insert records into table.');
        }
    }

    public function update(string $table_name, array $column_values, ?array $where = null)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $update_columns = [];
        foreach ($columns as $column) {
            $update_columns[] = "{$column} = ?";
        }
        $update_columns = implode(', ', $update_columns);

        $query = "UPDATE {$table_name} SET {$update_columns}";

        if (!empty($where)) {
            // TODO: whereチェック
            $query .= ' WHERE ' . $where['condition'];
            $values = array_merge($values, $where['values']);
        }

        $stmt = $this->prepareStatement($query);
        $stmt = $this->bindParams($stmt, $values);

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to update records.');
        }
    }

    public function delete(string $table_name, array $where = null)
    {
        $query = "DELETE FROM {$table_name}";

        if (empty($where)) {
            $stmt = $this->prepareStatement($query);
        } else {
            // TODO: $whereチェック
            $query .= ' WHERE ' . $where['condition'];
            $values = $where['values'];

            $stmt = $this->prepareStatement($query);
            $stmt = $this->bindParams($stmt, $values);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('Failed to delete records.');
        }
    }

    public function softDelete(string $table_name, array $where = null)
    {
        // TODO: whereチェック
        $this->update(
            $table_name,
            ['is_deleted' => 1,],
            $where
        );
    }

    private function bindParams(mysqli_stmt $stmt, array $values)
    {
        $types = '';
        foreach ($values as $value) {
            $type = gettype($value);
            if (!isset($this->bind_types[$type])) {
                throw new LogicException("Invalid type argument '{$type}' passed.");
            }

            $types .= $this->bind_types[$type];
        }

        if (!$stmt->bind_param($types, ...$values)) {
            throw new LogicException('Failed to bind params to statement.');
        }

        return $stmt;
    }

    private function prepareStatement(string $query)
    {
        if (!($stmt = $this->conn->prepare($query))) {
            throw new LogicException('Failed to prepare statement.');
        }

        return $stmt;
    }

    public function connect()
    {
        $config = $this->config;

        $host = $config['host'];
        if (isset($config['port']) && !empty($config['port'])) {
            $host .= ':' . $config['port'];
        }

        $conn = mysqli_connect($host, $config['user'], $config['password'], $config['name']);

        if (!$conn) {
            throw new Exception(__METHOD__ . "() Can't connect to the database server. " . mysqli_error($this->conn));
        }

        if (isset($config['charset'])) {
            mysqli_set_charset($conn, $config['charset']);
        }

        return $conn;
    }
}