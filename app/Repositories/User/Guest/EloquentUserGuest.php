<?php

namespace App\Repositories\User\Guest;

use App\Models\Guest;
use App\Models\Invite;
use App\Support\Config\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class EloquentUserGuest implements UserGuestRepository
{

    public function list(string $username, ?string $search = null)
    {
        $baseQuery = DB::table('guests as g')
            ->leftJoin('events as e', 'g.event_id', '=', 'e.id')
            ->leftJoin('invites as i', 'g.invite_id', '=', 'i.id')
            ->when(isNotNullString($username), fn($q) => $q->where('g.user_username', $username))
            ->when($search, fn($q) => $q->where('i.name', 'like', "%{$search}%"))
            ->select('g.*', 'e.type as event_type', 'e.date as event_date', 'i.name as invite_name');

        $paginate = (clone $baseQuery)
            ->orderBy('g.created_at', 'desc')
            ->paginate(Config::PAGINATE)
            ->toArray();

        $paginate['data'] = array_map(fn($item) => (array)$item, $paginate['data']);

        $summaryQuery = DB::table('guests as g')
            ->when(isNotNullString($username), fn($q) => $q->where('g.user_username', $username));

        $paginate['total_usd'] = (float)$summaryQuery->clone()->sum('amount_usd');
        $paginate['total_khr'] = (float)$summaryQuery->clone()->sum('amount_khr');

        $bankTransfer = $summaryQuery->clone()
            ->selectRaw('SUM(amount_usd) as usd, SUM(amount_khr) as khr')
            ->where('payment_option', 'bank')
            ->first();

        $cash = $summaryQuery->clone()
            ->selectRaw('SUM(amount_usd) as usd, SUM(amount_khr) as khr')
            ->where('payment_option', 'cash')
            ->first();

        $paginate['bank'] = [
            'usd' => (float)($bankTransfer->usd ?? 0),
            'khr' => (float)($bankTransfer->khr ?? 0),
        ];

        $paginate['cash'] = [
            'usd' => (float)($cash->usd ?? 0),
            'khr' => (float)($cash->khr ?? 0),
        ];

        return $paginate;
    }

    public function event(string $username)
    {
        return DB::table('events')
            ->where('user_username', $username)
            ->orderByDesc('created_at')
            ->limit(25)
            ->get([
                'id as key',
                'type as value',
                'date as date',
            ])->toArray();
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $guest = new Guest();
            $guest->invite_id = $data['invite_id'];
            $guest->event_id = $data['event_id'];
            $guest->user_username = $data['user_username'];
            $guest->description = $data['description'] ?? null;
            $guest->amount_usd = $data['amount_usd'];
            $guest->amount_khr = $data['amount_khr'];
            $guest->payment_option = $data['payment_option'];

            $guest->save();

            DB::table('invites')
                ->where('id', $data['invite_id'])
                ->update(['is_join' => true]);

            DB::commit();

            return $guest->toArray();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function edit(int $id)
    {
        // TODO: Implement edit() method.
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $guest = Guest::find($id);
            $guest->event_id = $data['event_id'];
            $guest->user_username = $data['user_username'];
            $guest->description = $data['description'] ?? null;
            $guest->amount_usd = $data['amount_usd'];
            $guest->amount_khr = $data['amount_khr'];
            $guest->payment_option = $data['payment_option'];
            $guest->save();

            DB::commit();

            return $guest->toArray();
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function searchInviteByEvent($eventId, $query)
    {
        return DB::table('invites')
            ->where('event_id', $eventId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('code', $query);
            })
            ->first();
    }

    public function delete(int $id)
    {
        $guest = Guest::findOrFail($id);

        $inviteId = $guest->invite_id;
        $guest->delete();
        Invite::where('id', $inviteId)->update(['is_join' => false]);
    }
}
