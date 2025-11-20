<?php

namespace App\Repositories\User\Guest;

interface UserGuestRepository
{
    public function list(string $username, ?string $search = null);

    public function event(string $username);

    public function store(array $data);

    public function edit(int $id);

    public function update(int $id, array $data);

    public function searchInviteByEvent($eventId, $query);

    public function delete(int $id);
}
