<?php

namespace App\Plugin\ModelView;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Collection;
use JsonSerializable;

class MenuItem implements JsonSerializable
{
    protected string $title;
    protected ?string $route = null;
    protected ?string $href = null;
    protected ?string $icon = null;
    protected ?array $activePath = null;
    protected ?array $permissions = null;
    protected Collection $children;
    protected string $target;

    public function __construct($title)
    {
        $this->title = $title;
        $this->target = "_self";
        $this->children = new Collection();
    }

    public static function create($title): MenuItem
    {
        return new self($title);
    }

    public function route($route): MenuItem
    {
        $this->route = $route;

        return $this;
    }

    public function href($href): MenuItem
    {
        $this->href = $href;

        return $this;
    }


    public function icon($icon): MenuItem
    {
        $this->icon = $icon;

        return $this;
    }

    public function active($path): MenuItem
    {
        $this->activePath = $path;

        return $this;
    }

    public function target($target): MenuItem
    {
        $this->target = $target;

        return $this;
    }

    public function getExpandedPath()
    {
        if (!$this->children->count()) {
            return null;
        }

        return $this->children->toBase()->map(function (MenuItem $item) {
            return $item->getActivePath();
        })->toArray();
    }

    public function getActivePath(): array|string|null
    {
        return $this->activePath;
    }

    public function getHref(): ?string
    {
        if ($this->href) {
            return $this->href;
        }
        if ($this->route) {
            try {
                return route($this->route);
            } catch (\Exception $e) {
                // Route doesn't exist, return null
                return null;
            }
        }
        return null;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function permissions($permissions): MenuItem
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function isDropdown(): bool
    {
        return $this->children && $this->children->count();
    }

    public function children(): Collection
    {
        return $this->children;
    }

    public function addChildren(array $children): MenuItem
    {

        foreach ($children as $child) {
            $this->children->push($child);
        }

        return $this;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function jsonSerialize(): array
    {
        // Use getHref() method which handles route resolution and null cases
        $href = $this->getHref();

        return [
            "title" => $this->title,
            "href" => $href,
            "icon" => $this->icon ?? null,
            "active" => !empty($this->activePath) && Request::is($this->activePath),
            "target" => $this->target,
            "child" => $this->children->jsonSerialize(),
        ];
    }
}
