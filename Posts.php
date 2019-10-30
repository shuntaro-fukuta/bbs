<?php

require_once('functions.php');

// adv: 例えばユーザー情報を扱うテーブルが増えたときにどうなるか考えましょう
class Posts
{
    private $db_instance;
    private $table_name = 'posts';
    private $bind_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'password'   => 's',
        'created_at' => 's',
    ];

    public function __construct($db_instance)
    {
        $this->setDatabaseInstance($db_instance);
    }

    private function setDatabaseInstance($db_instance)
    {
        // adv: 説明を聞いても意味がわからなかったけど、ここと他の例外の違いはなんなの？
        //      ここも他もこのクラスからしたら違いはなくて、このクラスのしごとがこれ以上できないから例外を吐いているはず
        //      その例外をどう扱うかは使う側の責任
        // adv: instanceof 使うなら引数の型指定すればいいのでは？
        try {
            if (!($db_instance instanceof mysqli)) {
                throw new InvalidArgumentException('Argument of ' . __FUNCTION__ . ' must be an instance of mysqli class. (' . __FILE__ . ' : ' . __LINE__ . ')');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        $this->db_instance = $db_instance;
    }

    public function selectRecord(array $columns, array $where)
    {
        $columns = implode(',', $columns);
        $query   = "SELECT {$columns} FROM {$this->table_name}";

        // adv: この名前変じゃない？
        //      この名前だと $where_parameters = $options['where'] と同じでは
        $where_parameters = $this->getWhereParameters($where);

        $query  .= $where_parameters['query'];
        $columns = $where_parameters['columns'];
        $values  = $where_parameters['values'];

        $stmt = $this->getParamBindedStatement($query, $columns, $values);
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
                // adv: この名前変じゃない？
                //      この名前だと $where_parameters = $options['where'] と同じでは
                $where_parameters = $this->getWhereParameters($options['where']);

                $query  .= $where_parameters['query'];
                $columns = $where_parameters['columns'];
                $values  = $where_parameters['values'];
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
            $stmt = $this->getParamBindedStatement($query, $columns, $values);
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

        $where_parameters = $this->getWhereParameters($wheres);

        $query  .= $where_parameters['query'];
        $columns = array_merge($columns, $where_parameters['columns']);
        $values  = array_merge($values, $where_parameters['values']);

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to update records.');
        }
    }

    public function delete(array $wheres)
    {
        $query = "DELETE FROM {$this->table_name}";

        $where_parameters = $this->getWhereParameters($wheres);

        $query  .= $where_parameters['query'];
        $columns = $where_parameters['columns'];
        $values  = $where_parameters['values'];

        $stmt = $this->getParamBindedStatement($query, $columns, $values);

        if (!$stmt->execute()) {
            throw new LogicException('Failed to delete records.');
        }
    }

    private function getParamBindedStatement(string $query, array $columns, array $values)
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

    // メソッド名
    private function getWhereParameters(array $wheres)
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

        $where_parameters = [];

        $where_parameters['query']   = $query;
        $where_parameters['columns'] = $columns;
        $where_parameters['values']  = $values;

        return $where_parameters;
    }
}