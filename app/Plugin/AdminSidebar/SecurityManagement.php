<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class SecurityManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $loginLogs = MenuItem::create("Login Logs")
            ->route('loginlogs.index')
            ->active(['admin/login-logs', 'admin/login-logs/*']);

        $children = [$loginLogs];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));

        $icon = <<<HTML
            <i class="fa-solid fa-shield-halved"></i>
        HTML;

        return MenuItem::create("Security")
            ->route('loginlogs.index')
            ->icon($icon)
            ->active($activePaths)
            ->addChildren($children);
    }
}


