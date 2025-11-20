<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class UserSettingManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("User Setting")
            ->route("setting")
            ->active(["setting/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-solid fa-user"></i>
            HTML;

        return MenuItem::create("ព័ត៍មានអ្នកប្រើប្រាស់")
            ->route("setting")
            ->icon($icon)
            ->active($activePaths);
    }

}
