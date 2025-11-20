<?php

namespace App\Repositories\User\Subscription;

use App\Support\Config\Config;
use Illuminate\Support\Facades\DB;

class EloquentSubscription implements SubscriptionRepository
{

    public function list()
    {
        $query = DB::table('subscriptions as s')
            ->orderBy('s.order');

        $paginate = $query->paginate(Config::PAGINATE);

        $paginate->getCollection()->transform(function ($item) {
            return (array)$item;
        });

        return $paginate->toArray();
    }

    public function getPlans(int $id)
    {
        $plans = DB::table('plans')
            ->where('subscription_id', $id)
            ->orderBy('order')
            ->get();

        return response()->json($plans);
    }
}
