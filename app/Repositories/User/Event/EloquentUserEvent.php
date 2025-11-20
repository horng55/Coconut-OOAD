<?php

namespace App\Repositories\User\Event;

use Illuminate\Support\Facades\DB;
use App\Support\Config\Config;
use App\Models\Event;
use Exception;

class EloquentUserEvent implements UserEventRepository
{
    public function list(string $username, ?string $search = null)
    {
        $query = DB::table('events as e')
            ->where('e.user_username', $username);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('e.name', 'like', "%{$search}%")
                    ->orWhere('e.additional_name', 'like', "%{$search}%");
            });
        }
        $paginate = $query->orderBy('e.created_at', 'desc')
            ->paginate(Config::PAGINATE)
            ->toArray();

        $data = [];
        foreach ($paginate['data'] as $index => $item) {
            $item = (array)$item;
            $data[$index] = $item;
        }
        $paginate['data'] = $data;

        return $paginate;
    }

    public function preview(string $slug)
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();
        $event->background = json_decode($event->background);

        return $event;
    }

    public function detail(string $username)
    {
        return Event::query()->where('user_username', $username)->first();
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $event = new Event();
            $event->slug = $data['slug'];
            $event->name = $data['name'];
            $event->additional_name = $data['additional_name'] ?? null;
            $event->description = $data['description'] ?? null;
            $event->location = $data['location'] ?? null;
            $event->type = $data['type'] ?? 'wedding';
            $event->phone = $data['phone'];
            $event->date = $data['date'];
            $event->user_username = $data['user_username'];
            $event->background = json_encode($data['background']) ?? null;

            $event->save();
            DB::commit();

            return $event->toArray();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function edit(int $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return null;
        }

        $event->background = json_decode($event->background);

        return $event;
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $event = Event::find($id);
            $event->name = $data['name'];
            $event->additional_name = $data['additional_name'] ?? null;
            $event->description = $data['description'] ?? null;
            $event->location = $data['location'] ?? null;
            $event->type = $data['type'] ?? 'wedding';
            $event->phone = $data['phone'];
            $event->date = $data['date'];
            $event->background = json_encode($data['background']) ?? null;
            $event->save();

            DB::commit();

            $payload = $event->toArray();
            $payload['background'] = json_decode($payload['background']);
            return $payload;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function delete(int $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
    }

    public function canDelete($id)
    {
        $hasGuests = DB::table("guests")
            ->where("event_id", $id)
            ->exists();
        
        $hasInvites = DB::table("invites")
            ->where("event_id", $id)
            ->exists();
        
        return !$hasGuests && !$hasInvites;
    }
}
