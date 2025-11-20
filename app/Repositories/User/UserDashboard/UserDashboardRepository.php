<?php

namespace App\Repositories\User\UserDashboard;

interface UserDashboardRepository
{
    public function info(string $username);

    public function infoByEvent(string $username);
}
