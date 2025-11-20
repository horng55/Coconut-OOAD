<?php

namespace App\Http\Controllers\Web\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        $query = UserAction::whereIn('action_type', ['login', 'login_failed'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_username', 'like', "%{$search}%")
                  ->orWhere('ip', 'like', "%{$search}%");
            });
        }

        if ($request->has('portal')) {
            $query->where('portal', $request->portal);
        }

        if ($request->has('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        $logs = $query->paginate(20);

        return Inertia::render('LoginLog/Index', [
            'paginate' => $logs,
            'search' => $request->search,
            'filters' => $request->only(['portal', 'action_type']),
        ]);
    }
}

