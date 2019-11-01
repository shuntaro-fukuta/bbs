<?php

require_once('db_connect.php');

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

        $results = $this->selectRecords($columns, $options);

        // ebine
        // 100% あるとは限らないからこのコードはだめ
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
            if (!($stmt = $this->mysqli->prepare($query))) {
                throw new LogicException('Failed to prepare statement.');
            }
        }

        $stmt->execute();

        if (!($results = $stmt->get_result())) {
            throw new LogicException('Failed to select records from table.');
        }

        return $results->fetch_all(MYSQLI_ASSOC);
    }

    // ebine
    // これってなんで where 受け取らないの？
    // 使いものにならない
    public function count()
    {
        $query = "SELECT COUNT(*) FROM {$this->table_name}";

        $count = (int) $this->mysqli->query($query)->fetch_assoc()['COUNT(*)'];

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

            // ebine
            /*
            $stmt = $this->mysqli->prepare($query);
            $this->bindParams($stmt, $column, $values);
            */

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
            $stmt = $this->mysqli->prepare($query);
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

    // ebine
    // 仕事が多すぎで、そのためにメソッドもよくわからんことになってる
    protected function getParamBindedStatement(string $query, array $columns, array $values)
    {
        $types = '';
        foreach ($columns as $column) {
            // ebine
            // $this->bind_types[$column] って100%はないよね
            $types .= $this->bind_types[$column];
        }

        if (!($stmt = $this->mysqli->prepare($query))) {
            throw new LogicException('Failed to prepare statement.');
        }

        if (!$stmt->bind_param($types, ...$values)) {
            throw new LogicException('Failed to bind param to statement.');
        }

        return $stmt;
    }

    protected function getWhereBindItems(array $wheres)
    {
        if ($wheres === []) {
            throw new InvalidArgumentException('Where condition is required.');
        }

        // ebine
        // 実装がきたない
        // options の where, and, or が並列で考えられてるよね
        // options[where] が条件式で、and, or はその中の話
        // イメージ(例)
        /*
        $options['where'] = [
            ['id', '>=', 10],
            ['age', '<=', 60, 'OR'],
        ];
        id >= 10 OR age <= 60
        */

        // wheresのキーのフォーマットチェック
        $available_options = ['and', 'or'];

        $where_keys = array_keys($wheres);
        $keys_count = count($where_keys);

        for ($i = 0; $i < $keys_count; $i++) {
            if ($i === 0) {
                if ($where_keys[$i] !== 'where') {
                    throw new InvalidArgumentException("Where condition's first key must be 'where'.");
                }
            } else {
                if (!in_array($where_keys[$i], $available_options)) {
                    throw new InvalidArgumentException("Avalable where options are these [" . implode(', ' ,$available_options) . '].');
                }
            }
        }

        $query   = '';
        $columns = [];
        $values  = [];

        foreach ($wheres as $key => $where) {
            if (!is_array($where)) {
                throw new InvalidArgumentException("Where's conditions must be array.");
            }

            $column   = $where[0] ?? null;
            $operator = $where[1] ?? null;
            $value    = $where[2] ?? null;

            if (!is_string($column)) {
                throw new InvalidArgumentException("Where's first parameter must be string.");
            }
            if (!in_array($operator, ['<', '>', '=', '!=', '<=', '>='])) {
                throw new InvalidArgumentException("Where's second parameter must be one of these [<, >, =, !=, <=, >=].");
            }
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException("Where's third parameter must be string or integer.");
            }

            if ($key === 'where' || in_array($key, $available_options)) {
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