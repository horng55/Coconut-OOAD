<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class EventManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("Event Management")
            ->route("events.index")
            ->active(["events/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-solid fa-paste"></i>
            HTML;

        return MenuItem::create("កម្មវិធី")
            ->route("events.index")
            ->icon($icon)
            ->active($activePaths);
    }

}
