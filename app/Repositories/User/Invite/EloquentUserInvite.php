<?php

namespace App\Repositories\User\Invite;

use App\Actions\Invite\InviteCodeGenerator;
use App\Models\Invite;
use App\Support\Config\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class EloquentUserInvite implements UserInviteRepository
{

    public function list(string $username, ?string $search = null)
    {
        $query = DB::table('invites as i')
            ->join('events as e', 'i.event_id', '=', 'e.id')
            ->where('i.user_username', $username)
            ->select(
                'i.*',
                'e.name as event_name',
                'e.type as event_type',
                'e.date as event_date',
                'e.slug as event_slug'
            )
            ->orderBy('i.created_at', 'desc');

        // Add search condition if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('i.name', 'like', '%' . $search . '%')
                    ->orWhere('i.code', 'like', '%' . $search . '%');
            });
        }

        // Execute pagination
        $paginate = $query->paginate(Config::PAGINATE)->toArray();

        // Convert objects to arrays
        foreach ($paginate['data'] as $index => $item) {
            $paginate['data'][$index] = (array)$item;
        }

        return $paginate;
    }

    public function detail(string $username)
    {
        $invite = Invite::with(['event' => function ($query) {
            $query->select('*');
        }])
            ->select('id', 'code', 'name', 'event_id', 'user_username')
            ->where('user_username', $username)
            ->firstOrFail();

        if ($invite->event && is_string($invite->event->background)) {
            $invite->event->background = json_decode($invite->event->background);
        }

        return $invite->toArray();
    }

    public function preview(string $code, string $username)
    {
        $invite = Invite::with(['event' => function ($query) {
            $query->select('*');
        }])
            ->select('id', 'code', 'name', 'event_id', 'user_username')
            ->where('code', $code)
            ->where('user_username', $username)
            ->firstOrFail();

        if ($invite->event && is_string($invite->event->background)) {
            $invite->event->background = json_decode($invite->event->background);
        }

        return $invite->toArray();
    }

    public function previewByCode(string $code)
    {
        // Public access - find invite by code only (no username required)
        $invite = Invite::with(['event' => function ($query) {
            $query->select('*');
        }])
            ->select('id', 'code', 'name', 'event_id', 'user_username')
            ->where('code', $code)
            ->firstOrFail();

        if ($invite->event && is_string($invite->event->background)) {
            $invite->event->background = json_decode($invite->event->background);
        }

        return $invite->toArray();
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
            $invite = new Invite();
            $invite->code = $data['code'] ?? "";
            $invite->name = $data['name'];
            $invite->event_id = $data['event_id'];
            $invite->user_username = $data['user_username'];

            $invite->save();

            $inviteId = $invite->id;

            if (!$invite->code) {
                $invite->code = InviteCodeGenerator::generate($inviteId);
                $invite->save();
            }

            DB::commit();

            return $invite->toArray();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function edit(int $id)
    {
        return Invite::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $invite = Invite::find($id);
            $invite->code = $data['code'] ?? "";
            $invite->name = $data['name'];
            $invite->event_id = $data['event_id'];
            $invite->save();

            $inviteId = $invite->id;

            if (!$invite->code) {
                $invite->code = InviteCodeGenerator::generate($inviteId);
                $invite->save();
            }

            DB::commit();
            return $invite->toArray();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function canDelete($id)
    {
        $exists = DB::table("guests")
            ->where("invite_id", $id)
            ->exists();
        return !$exists;
    }

    public function delete(int $id)
    {
        $invite = Invite::findOrFail($id);
        $invite->delete();
    }
}
