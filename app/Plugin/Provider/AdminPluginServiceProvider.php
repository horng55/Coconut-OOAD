<?php

namespace App\Plugin\Provider;

use App\Plugin\Admin;
// use App\Plugin\AdminSidebar\AdminSubscriptionManagement; // Disabled - not needed for school management
// use App\Plugin\AdminSidebar\AdsManagement; // Disabled - not needed for school management
use App\Plugin\AdminSidebar\DashboardManagement;
// use App\Plugin\AdminSidebar\PostManagement; // Disabled - not needed for school management
// use App\Plugin\AdminSidebar\ProductManagement; // Disabled - not needed for school management
// use App\Plugin\AdminSidebar\ShowcaseManagement; // Disabled - not needed for school management
// use App\Plugin\AdminSidebar\BankOptionManagement; // Disabled - not needed for school management
use App\Plugin\AdminSidebar\UserManagement;
use App\Plugin\AdminSidebar\SecurityManagement;
// use App\Plugin\AdminSidebar\PromotionManagement; // Disabled - not needed for school management
// use App\Plugin\AdminSidebar\EventManagement; // Disabled - not needed for school management
use App\Plugin\AdminSidebar\SchoolManagement;
use Illuminate\Support\ServiceProvider;

class AdminPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $instances = [];
        foreach ($this->plugins() as $plugin) {
            $instances[] = $this->app->register($plugin);
        }
        Admin::plugins($instances);
    }

    protected function plugins(): array
    {
        return [
            DashboardManagement::class,
            SchoolManagement::class,
            UserManagement::class,
            SecurityManagement::class,
            // EventManagement::class, // Disabled - not needed for school management
            // PostManagement::class, // Disabled - not needed for school management
            // ProductManagement::class, // Disabled - not needed for school management
            // AdsManagement::class, // Disabled - not needed for school management
            // AdminSubscriptionManagement::class, // Disabled - not needed for school management
            // ShowcaseManagement::class, // Disabled - not needed for school management
            // BankOptionManagement::class, // Disabled - not needed for school management
            // PromotionManagement::class, // Disabled - not needed for school management
        ];
    }
}
