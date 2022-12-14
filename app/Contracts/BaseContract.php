<?php

namespace App\Contracts;

interface BaseContract
{
    public function create(array $attributes): mixed;

    public function update(array $attributes, int $id): mixed;

    public function all($columns = array("*"), string $orderBy = "id", string $sortBy = "asc"): mixed;

    public function findOneOrFail(int $id): mixed;

    public function findBy(array $data): mixed;

    public function findOneByOrFail(array $data): mixed;

    public function delete(int $id): mixed;
}
