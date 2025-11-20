<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class ReportManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $reports = MenuItem::create("Reports")
            ->route("admin.reports.index")
            ->active(["admin/reports/*"]);

        $children = [$reports];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
            <i class="fas fa-chart-bar"></i>
        HTML;

        return MenuItem::create("Report Management")
            ->route("admin.reports.index")
            ->icon($icon)
            ->active($activePaths)
            ->addChildren($children);
    }
}

