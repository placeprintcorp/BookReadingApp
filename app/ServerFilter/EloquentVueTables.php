<?php

namespace App\ServerFilter;

use Illuminate\Database\Eloquent\Builder;

class EloquentVueTables
{
    /**
     * Database connection used.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * @param \Illuminate\Database\Query\Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->connection = $builder->getConnection();
    }

    public function get($model, array $fields)
    {
        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));

        $data = $model;

        if (isset($query) && $query) {
            $data = $this->filter($data, $query, $fields);
        }

        $count = $model->count();

        $data->limit($limit)->skip($limit * ($page - 1));

        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        } else {
            $data->latest();
        }

        $results = $data->get()->toArray();

        return [
            'data'  => $results,
            'count' => $count,
        ];
    }

    protected function filter($data, $keyword, $fields)
    {
        return $data->where(function ($q) use ($keyword, $fields) {
            foreach ($fields as $index => $field) {
                $method = $index ? 'orWhereRaw' : 'WhereRaw';
                $field = $this->castColumn($field);
                $sql = $field . $this->sqlLikeString();
                $q->{$method}($sql, ["%$keyword%"]);
            }
        });
    }

    protected function castColumn($column)
    {
        switch ($this->connection->getDriverName()) {
            case 'pgsql':
                return 'CAST(' . $column . ' as TEXT)';
            default:
                return $column;
        }
    }

    protected function sqlLikeString()
    {
        switch ($this->connection->getDriverName()) {
            case 'pgsql':
                return ' ILIKE ?';
            default:
                return ' LIKE ?';
        }
    }
}
