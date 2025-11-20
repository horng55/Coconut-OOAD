<?php

namespace App\Plugin\AdminSidebar;

use App\Plugin\Interface\Plugin;
use App\Plugin\ModelView\MenuItem;

class SchoolManagement extends Plugin
{
    public function sidebar(): MenuItem
    {
        $teachers = MenuItem::create("Teachers")
            ->route("admin.teachers.index")
            ->active(["admin/teachers/*"]);

        $students = MenuItem::create("Students")
            ->route("admin.students.index")
            ->active(["admin/students/*"]);

        $subjects = MenuItem::create("Subjects")
            ->route("admin.subjects.index")
            ->active(["admin/subjects/*"]);

        $classes = MenuItem::create("Classes")
            ->route("admin.classes.index")
            ->active(["admin/classes/*"]);

        $assignments = MenuItem::create("Assignments")
            ->route("admin.assessments.index")
            ->active(["admin/assessments/*"]);

        $grades = MenuItem::create("Grades")
            ->route("admin.grades.index")
            ->active(["admin/grades/*"]);

        $children = [$teachers, $students, $subjects, $classes, $assignments, $grades];
        $activePaths = array_merge(...array_map(fn($item) => $item->getActivePath(), $children));
        $icon = <<<HTML
            <i class="fa-solid fa-school"></i>
        HTML;

        return MenuItem::create("School Management")
            ->route("admin.teachers.index")
            ->icon($icon)
            ->active($activePaths)
            ->addChildren($children);
    }
}

