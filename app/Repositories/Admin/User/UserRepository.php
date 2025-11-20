<?php

namespace App\Repositories\Admin\User;

interface UserRepository
{
    public function list(?string $search);

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function delete(int $id);
}
