<?php

namespace App\Http\Middleware;

use App\Plugin\Admin;
use App\Plugin\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class UserHandleInertiaRequests extends Middleware
{

    protected $rootView = 'app';


    public function version(Request $request): ?string
    {
        return parent::version($request);
    }


    public function share(Request $request): array
    {
        $sharedData = parent::share($request);
        return array_merge($sharedData, [
            'csrf_token' => csrf_token(),
            'current_user' => json_decode(session()->get('current_user')),
            'user_sidebars' => User::getSidebar(),
            'flash' => [
                'alert' => $request->session()->get('alert'),
            ],
        ]);
    }

}
