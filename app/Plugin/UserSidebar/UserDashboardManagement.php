<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class UserDashboardManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("Dashboard")
            ->route("dashboard")
            ->active(["dashboard/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-brands fa-microsoft"></i>
            HTML;

        return MenuItem::create("ផ្ទាំងបង្ហាញទូទៅ")
            ->route("dashboard")
            ->icon($icon)
            ->active($activePaths);
    }

}
