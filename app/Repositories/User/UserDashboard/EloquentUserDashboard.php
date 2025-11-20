<?php

namespace App\Repositories\User\UserDashboard;

use Illuminate\Support\Facades\DB;

class EloquentUserDashboard implements UserDashboardRepository
{
    public function info(string $username)
    {
        $totalGuests = DB::table('guests as g')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            })
            ->count();

        $totalUsd = DB::table('guests as g')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            })
            ->sum('amount_usd');

        $totalKhr = DB::table('guests as g')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            })
            ->sum('amount_khr');

        $bank = DB::table('guests as g')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            })
            ->where('g.payment_option', 'bank')
            ->selectRaw('SUM(g.amount_usd) as usd, SUM(g.amount_khr) as khr')
            ->first();

        $cash = DB::table('guests as g')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            })
            ->where('g.payment_option', 'cash')
            ->selectRaw('SUM(g.amount_usd) as usd, SUM(g.amount_khr) as khr')
            ->first();

        $totalEvents = DB::table('events')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('user_username', $username);
            })
            ->count();

        return [
            'total_guests' => $totalGuests,
            'total_usd' => $totalUsd ?? 0,
            'total_khr' => $totalKhr ?? 0,
            'total_events' => $totalEvents,
            'bank' => [
                'usd' => $bank->usd ?? 0,
                'khr' => $bank->khr ?? 0,
            ],
            'cash' => [
                'usd' => $cash->usd ?? 0,
                'khr' => $cash->khr ?? 0,
            ],
        ];
    }

    public function infoByEvent(string $username)
    {

        $baseQuery = DB::table('guests as g')
            ->join('events as e', 'g.event_id', '=', 'e.id')
            ->when(isNotNullString($username), function ($q) use ($username) {
                $q->where('g.user_username', $username);
            });

        return $baseQuery
            ->select(
                'g.event_id',
                'e.type as event_type',
                'e.date as event_date',
                DB::raw('COUNT(g.id) as total_guests'),
                DB::raw('SUM(g.amount_usd) as total_usd'),
                DB::raw('SUM(g.amount_khr) as total_khr'),
                DB::raw("SUM(CASE WHEN g.payment_option = 'bank' THEN g.amount_usd ELSE 0 END) as bank_usd"),
                DB::raw("SUM(CASE WHEN g.payment_option = 'bank' THEN g.amount_khr ELSE 0 END) as bank_khr"),
                DB::raw("SUM(CASE WHEN g.payment_option = 'cash' THEN g.amount_usd ELSE 0 END) as cash_usd"),
                DB::raw("SUM(CASE WHEN g.payment_option = 'cash' THEN g.amount_khr ELSE 0 END) as cash_khr")
            )
            ->groupBy('g.event_id', 'e.type', 'e.date')
            ->get();
    }
}
