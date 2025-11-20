<?php

namespace App\Plugin\Interface;

use App\Plugin\ModelView\MenuItem;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function sidebar(): ?MenuItem
    {
        return null;
    }

    public function boot()
    {
        return null;
    }
}
