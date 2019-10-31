<?php

abstract class Table
{
    abstract public function __construct(mysqli $db_instance);

    public function selectRecord(array $columns, array $where)
    {
        $columns = implode(',', $columns);
        $query   = "SELECT {$columns} FROM {$this->table_name}";

        $where_bind_items = $this->getWhereBindItems($where);

        $query        .= $where_bind_items['query'];
        $where_columns = $where_bind_items['columns'];
        $where_values  = $where_bind_items['values'];

        $stmt = $this->getParamBindedStatement($query, $where_columns, $where_values);

        $stmt->execute();

        if (!($results = $stmt->get_result())) {
            throw new LogicException('Failed to select record from table.');
        }

        return $results->fetch_assoc();
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

    // adv: 変数に入っている「物」がなんなのかを全然考えれられてないです
    //      変数の中身のコードを説明はしているけど…
    //      例えば $column_values はコード上カラム名と値が入っている名前にはなっているけど、
    //      それが「なに」かっていう情報が入ってないよね
    //      $column_with_placeholdersって更新する内容でも更新対象の条件でも同じ変数名にならない？
    public function update(array $column_values, array $wheres)
    {
        $columns = array_keys($column_values);
        $values  = array_values($column_values);

        $column_with_placeholders = [];
        foreach ($columns as $column) {
            $column_with_placeholders[] = "{$column} = ?";
        }
        $column_with_placeholders = implode(', ', $column_with_placeholders);

        $query = "UPDATE {$this->table_name} SET {$column_with_placeholders}";

        $where_bind_items = $this->getWhereBindItems($wheres);

        $query  .= $where_bind_items['query'];
        $columns = array_merge($columns, $where_bind_items['columns']);
        $values  = array_merge($values, $where_bind_items['values']);

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to update records.');
        }
    }

    public function delete(array $wheres)
    {
        $query = "DELETE FROM {$this->table_name}";

        $where_bind_items = $this->getWhereBindItems($wheres);

        $query  .= $where_bind_items['query'];
        $columns = $where_bind_items['columns'];
        $values  = $where_bind_items['values'];

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

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
        $query   = '';
        $columns = [];
        $values  = [];

        foreach ($wheres as $key => $where) {
            $column   = $where[0];
            $operator = $where[1];
            $value    = $where[2];

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

        $where_bind_items = [];

        $where_bind_items['query']   = $query;
        $where_bind_items['columns'] = $columns;
        $where_bind_items['values']  = $values;

        return $where_bind_items;
    }
}