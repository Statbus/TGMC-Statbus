<?php

namespace App\Service;

class HotbarCreationService
{
    public static $hotbar;

    public static function create(): static
    {
        static::$hotbar = [];
        return new static();
    }

    public static function addLink(
        string $title,
        string $icon,
        string $url,
        array $params = []
    ): static {
        static::$hotbar[] = [
            'title' => $title,
            'icon' => $icon,
            'url' => $url,
            'params' => $params
        ];
        return new static();
    }

    public static function get(): array
    {
        return static::$hotbar;
    }

}
