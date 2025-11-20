<?php

namespace App\Repositories\User\UserSubscription;

interface UserSubscriptionRepository
{
    public function list(string $username);

    public function current(string $username);
}
