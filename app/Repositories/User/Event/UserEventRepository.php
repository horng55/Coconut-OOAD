<?php

namespace App\Repositories\User\Event;

interface UserEventRepository
{
    public function list(string $username, ?string $search = null);

    public function preview(string $slug);

    public function detail(string $username);

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function canDelete($id);
}
