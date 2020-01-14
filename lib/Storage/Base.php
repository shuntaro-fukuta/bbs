<?php

abstract class Storage_Base
{
    protected $database = null;
    protected $table_name = '';

    public function __construct()
    {
        $this->database = new Storage_Database_MySQL();
    }

    public function selectRecord(array $columns, array $where)
    {
        return $this->database->selectRecord($this->table_name, $columns, $where);
    }

    public function selectRecords(array $columns, array $options = null)
    {
        return $this->database->selectRecords($this->table_name, $columns, $options);
    }

    public function count(array $where = null)
    {
        return $this->database->count($this->table_name, $where);
    }

    public function insert(array $column_values)
    {
        $this->database->insert($this->table_name, $column_values);
    }

    public function update(array $column_values, array $where = null)
    {
        $this->database->update($this->table_name, $column_values, $where);
    }

    public function delete(array $where = null)
    {
        $this->database->delete($this->table_name, $where);
    }

    public function softDelete(array $where)
    {
        $this->update(
            ['is_deleted' => 1],
            $where
        );
    }

    public function escape(string $value, bool $withQuotes = true)
    {
        return $this->database->escape($value, $withQuotes);
    }
}