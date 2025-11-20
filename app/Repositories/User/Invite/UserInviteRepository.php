<?php

namespace App\Repositories\User\Invite;

interface UserInviteRepository
{
    public function list(string $username, ?string $search = null);

    public function detail(string $username);

    public function preview(string $code, string $username);

    public function previewByCode(string $code);

    public function event(string $username);

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function canDelete($id);

    public function delete(int $id);
}
