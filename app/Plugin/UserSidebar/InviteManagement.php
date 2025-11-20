<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class InviteManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("Invite Management")
            ->route("invites.index")
            ->active(["invites/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-solid fa-envelope"></i>
            HTML;

        return MenuItem::create("អញ្ជើញភ្ញៀវ")
            ->route("invites.index")
            ->icon($icon)
            ->active($activePaths);
    }

}
