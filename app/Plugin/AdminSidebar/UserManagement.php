<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class UserManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $users = MenuItem::create("Users")
            ->route("users.index")
            ->active(["users/*"]);

        $roles = MenuItem::create("Roles")
            ->route("admin.roles.index")
            ->active(["admin/roles/*"]);

        $children = [$users, $roles];

        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));

        $icon = <<<HTML
                <i class="fa-solid fa-users"></i>
             HTML;

        return MenuItem::create("Users Management")
            ->icon($icon)
            ->active($activePaths)
            ->addChildren($children);
    }
}
