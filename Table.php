<?php

require_once('functions.php');

abstract class Table
{
    protected $db_instance;
    protected $table_name;
    protected $bind_types;

    public function __construct(mysqli $db_instance)
    {
        $this->db_instance = $db_instance;
    }

    public function selectRecord(array $columns, array $where)
    {
        $options = [];

        $options['where'] = $where;
        $options['limit'] = 1;

        $results = $this->selectRecords($columns, $options);

        return $results[0];
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
                $query   .= ' LIMIT ' . (int) $options['limit'];

                if (isset($options['offset'])) {
                    $query   .= ' OFFSET ' . (int) $options['offset'];
                }
            }
        }

        if (isset($options) && isset($options['where'])) {
            $stmt = $this->getParamBindedStatement($query, $where_columns, $where_values);
        } else {
            if (!($stmt = $this->db_instance->prepare($query))) {
                throw new LogicException('Failed to prepare statement.');
            }
        }

        $stmt->execute();

        if (!($results = $stmt->get_result())) {
            throw new LogicException('Failed to select records from table.');
        }

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public function count()
    {
        $query = "SELECT COUNT(*) FROM {$this->table_name}";

        $count = (int) $this->db_instance->query($query)->fetch_assoc()['COUNT(*)'];

        return $count;
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

    public function update(array $column_values, array $wheres = null)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $update_columns = [];
        foreach ($columns as $column) {
            $update_columns[] = "{$column} = ?";
        }
        $update_columns = implode(', ', $update_columns);

        $query = "UPDATE {$this->table_name} SET {$update_columns}";

        if (is_null($wheres)) {
            $stmt = $this->getParamBindedStatement($query, $columns, $values);
        } else {
            $where_bind_items = $this->getWhereBindItems($wheres);

            $query  .= $where_bind_items['query'];
            $columns = array_merge($columns, $where_bind_items['columns']);
            $values  = array_merge($values, $where_bind_items['values']);

            $stmt = $this->getParamBindedStatement($query, $columns, $values);
        }

        if (!$stmt->execute()) {
            throw new LogicException('Failed to update records.');
        }
    }

    public function delete(array $wheres = null)
    {
        $query = "DELETE FROM {$this->table_name}";

        if (is_null($wheres)) {
            $stmt = $this->db_instance->prepare($query);
        } else {
            $where_bind_items = $this->getWhereBindItems($wheres);

            $query  .= $where_bind_items['query'];
            $columns = $where_bind_items['columns'];
            $values  = $where_bind_items['values'];

            $stmt = $this->getParamBindedStatement($query, $columns, $values);
        }

        if (!$stmt->execute()) {
            throw new LogicException('Failed to delete records.');
        }
    }

    protected function getParamBindedStatement(string $query, array $columns, array $values)
    {
        $types = '';
        foreach ($columns as $column) {
            $types .= $this->bind_types[$column];
        }

        if (!($stmt = $this->db_instance->prepare($query))) {
            throw new LogicException('Failed to prepare statement.');
        }

        if (!$stmt->bind_param($types, ...$values)) {
            throw new LogicException('Failed to bind param to statement.');
        }

        return $stmt;
    }

    protected function getWhereBindItems(array $wheres)
    {
        if (is_empty($wheres)) {
            throw new LogicException('Where condition is required.');
        }

        // whereのフォーマットチェック
        $where_keys = array_keys($wheres);
        $key_count  = count($where_keys);
        for ($i = 0; $i < $key_count; $i++) {
            if ($i === 0) {
                if ($where_keys[$i] !== 'where') {
                    throw new LogicException("Where condition's first key must be 'where'.");
                }
            } else {
                if ($where_keys[$i] !== 'or' || $where_keys[$i] !== 'and') {
                    throw new LogicException("Where condition's second and later keys must be 'and' or 'or'.");
                }
            }
        }

        $query   = '';
        $columns = [];
        $values  = [];

        foreach ($wheres as $key => $where) {
            $column   = $where[0];
            $operator = $where[1];
            $value    = $where[2];

            if (!is_string($column)) {
                throw new LogicException("Argument of where's first condition must be string.");
            }
            if (!in_array($operator, ['<', '>', '=', '<=', '=>'])) {
                throw new LogicException("Argument of where's second condition must be one of these ( <, >, =, <=, >=  )");
            }
            if (!is_string($value) || !is_numeric($value)) {
                throw new LogicException("Argument of where's third condition must be string or number.");
            }

            if ($key === 'where' || $key === 'and' || $key === 'or') {
                $query .= ' ' . strtoupper($key) . ' ';
            }

            $query .= "{$column} {$operator} ?";

            $columns[] = $column;
            $values[]  = $value;
        }

        $where_bind_items = [];

        $where_bind_items['query']   = $query;
        $where_bind_items['columns'] = $columns;
        $where_bind_items['values']  = $values;

        return $where_bind_items;
    }
}