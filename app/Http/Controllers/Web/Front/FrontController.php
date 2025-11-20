<?php

namespace App\Http\Controllers\Web\Front;

use AllowDynamicProperties;
use App\Http\Controllers\Controller;
use App\Repositories\Front\FrontRepository;
use Inertia\Inertia;

#[AllowDynamicProperties] class FrontController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }
}
