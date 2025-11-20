<?php

if (!function_exists('isNotNullString')) {
    function isNotNullString($value): bool
    {
        return !empty($value) && $value !== '' && $value !== 'null';
    }
}


if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower($text);

        $text = preg_replace('/[^a-z0-9]+/', '-', $text);

        return trim($text, '-');
    }

}

if (!function_exists('haversine')) {
    function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}


if (!function_exists('trackUserAction')) {
    function trackUserAction(string $username, int $id, string $actionableType, string $actionType, array $metadata = []): void
    {
    }
}

if (!function_exists('formatDatetime')) {
    function formatDatetime(string $datetime, string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, strtotime($datetime));
    }
}

