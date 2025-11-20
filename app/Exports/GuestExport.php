<?php

namespace App\Exports;

use App\Models\Guest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class GuestExport implements FromView
{
    protected string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function view(): View
    {
        $guests = DB::table('guests')
            ->join('invites', 'guests.invite_id', '=', 'invites.id')
            ->where('guests.user_username', $this->username)
            ->select('guests.*', 'invites.name as invite_name')
            ->get();

        return view('exports.guests', [
            'guests' => $guests,
        ]);
    }
}
