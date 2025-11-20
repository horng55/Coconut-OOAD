<?php

namespace App\Repositories\User\UserSubscription;

use App\Support\Config\Config;
use Illuminate\Support\Facades\DB;

class EloquentUserSubscription implements UserSubscriptionRepository
{
    public function list(string $username)
    {
        $query = DB::table('user_subscriptions as us')
            ->join('plans as p', 'us.plan_id', '=', 'p.id')
            ->join('subscriptions as s', 'p.subscription_id', '=', 's.id')
            ->select(
                'us.*',
                'p.name as plan_name',
                'p.description as plan_description',
                'p.price as plan_price',
                'p.duration as plan_duration',
                's.name as subscription_name'
            )
            ->where('us.user_username', $username)
            ->whereIn('us.status', ['canceled', 'expired'])
            ->orderBy('us.created_at', 'desc');

        $paginate = $query->paginate(Config::PAGINATE);

        $paginate->setCollection(
            $paginate->getCollection()->map(function ($item) {
                return (array)$item;
            })
        );

        return $paginate->toArray();
    }

    public function current(string $username)
    {
        // Optimize query: ensure proper ordering and limit to avoid scanning too many rows
        return DB::table('user_subscriptions as us')
            ->join('plans as p', 'us.plan_id', '=', 'p.id')
            ->join('subscriptions as s', 'p.subscription_id', '=', 's.id')
            ->where('us.user_username', $username)
            ->whereIn('us.approval_status', ['pending', 'approved'])
            ->where('us.status', 'active')
            ->where(function ($query) {
                $query->whereNull('us.end_at')
                    ->orWhere('us.end_at', '>', now());
            })
            ->select(
                'us.*',
                'p.name as plan_name',
                'p.description as plan_description',
                'p.price as plan_price',
                'p.duration as plan_duration',
                's.name as subscription_name'
            )
            ->orderBy('us.start_at', 'desc')
            ->first();
    }
}
