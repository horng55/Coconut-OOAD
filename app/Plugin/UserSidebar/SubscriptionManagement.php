<?php

namespace App\Plugin\UserSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class SubscriptionManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $dashboard = MenuItem::create("Subscription Management")
            ->route("subscription.index")
            ->active(["subscription/*"]);

        $children = [$dashboard];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
                <i class="fa-solid fa-paste"></i>
            HTML;

        return MenuItem::create("គម្រោងប្រើប្រាស់")
            ->route("subscription.index")
            ->icon($icon)
            ->active($activePaths);
    }

}
