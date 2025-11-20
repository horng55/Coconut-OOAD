<?php

namespace App\Plugin;

class User
{
    public static array $plugins = [];
    public static array $widgets = [];
    public static array $scripts = [];
    public static array $styles = [];
    public static array $hooks = [];

    public static function hook($name, $handler): void
    {
        self::$hooks[$name][] = $handler;
    }

    public static function hasHook($name): bool
    {
        return isset(self::$hooks[$name]);
    }

    public static function getHookHandlers($name)
    {
        return data_get(self::$hooks, $name);
    }

    public static function plugins(array $plugins): void
    {
        self::$plugins = array_merge(self::$plugins, $plugins);
    }

    public static function availablePlugins(): array
    {
        return self::$plugins;
    }

    public static function widgets(array $widgets): void
    {
        self::$widgets = array_merge(self::$widgets, $widgets);
    }

    public static function availableWidgets(): \Illuminate\Support\Collection
    {
        return collect(self::$widgets)->values();
    }

    public static function getSidebar(): array
    {
        return array_map(fn($plugin) => $plugin->sidebar()->jsonSerialize(), User::availablePlugins());
    }
}
