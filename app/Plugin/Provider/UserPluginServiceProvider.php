<?php

namespace App\Plugin\Provider;

use App\Plugin\User;
use App\Plugin\UserSidebar\EventManagement;
use App\Plugin\UserSidebar\GuestManagement;
use App\Plugin\UserSidebar\InviteManagement;
use App\Plugin\UserSidebar\SubscriptionManagement;
use App\Plugin\UserSidebar\UserDashboardManagement;
use App\Plugin\UserSidebar\UserSettingManagement;
use Illuminate\Support\ServiceProvider;

class UserPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $instances = [];
        foreach ($this->plugins() as $plugin) {
            $instances[] = $this->app->register($plugin);
        }
        User::plugins($instances);
    }

    protected function plugins(): array
    {
        return [
            UserDashboardManagement::class,
            GuestManagement::class,
            InviteManagement::class,
            EventManagement::class,
            SubscriptionManagement::class,
            UserSettingManagement::class,
        ];
    }
}
