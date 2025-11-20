<?php

namespace App\Repositories\User\Subscription;

interface SubscriptionRepository
{
    public function list();

    public function getPlans(int $id);
}
