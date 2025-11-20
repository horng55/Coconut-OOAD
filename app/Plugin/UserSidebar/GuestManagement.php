<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class GuestManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("Guest Management")
            ->route("guests.index")
            ->active(["guests/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-solid fa-users"></i>
            HTML;

        return MenuItem::create("ចំណងដៃភ្ញៀវ")
            ->route("guests.index")
            ->icon($icon)
            ->active($activePaths);
    }

}
