<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class AcademicsManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $attendance = MenuItem::create("Attendance")
            ->route("admin.attendances.index")
            ->active(["admin/attendances/*"]);

        $grades = MenuItem::create("Grades")
            ->route("admin.grades.index")
            ->active(["admin/grades/*"]);

        $announcements = MenuItem::create("Announcements")
            ->route("admin.announcements.index")
            ->active(["admin/announcements/*"]);

        $messages = MenuItem::create("Messages")
            ->route("admin.messages.index")
            ->active(["admin/messages/*"]);

        $children = [$attendance, $grades, $announcements, $messages];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
            <i class="fa-solid fa-book-open"></i>
        HTML;

        return MenuItem::create("Academics")
            ->route("admin.attendances.index")
            ->icon($icon)
            ->active($activePaths)
            ->addChildren($children);
    }
}

