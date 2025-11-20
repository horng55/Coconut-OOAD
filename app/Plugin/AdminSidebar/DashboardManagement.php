<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class DashboardManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $icon = <<<HTML
            <i class="fa-solid fa-chart-line"></i>
        HTML;

        return MenuItem::create("Dashboard")
            ->route("dashboard.index")
            ->icon($icon)
            ->active(["admin/dashboard"]);
    }
}

