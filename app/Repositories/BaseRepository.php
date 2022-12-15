<?php

namespace App\Repositories;

use App\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseContract
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes): mixed
    {
        return $this->model->create($attributes);
    }

    public function update(array $attributes, int $id): mixed
    {
        return $this->model->findOrFail($id)->update($attributes);
    }

    public function all($columns = array("*"), string $orderBy = "id", string $sortBy = "asc"): mixed
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    public function paginate(int $number, string $orderBy = "id", string $sortBy = "desc"): mixed
    {
        return $this->model->orderBy($orderBy, $sortBy)->paginate($number);
    }

    public function findOneOrFail(int $id): mixed
    {
        return $this->model->findOrFail($id);
    }

    public function findBy(array $data): mixed
    {
        return $this->model->where($data)->first();
    }

    public function findOneByOrFail(array $data): mixed
    {
        return $this->model->where($data)->firstOrFail();
    }

    public function delete(int $id): mixed
    {
        return $this->model->findOrFail($id)->delete();
    }
}
