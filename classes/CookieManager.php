<?php
class CookieManager
{
    public static function set($key, $value, $expireDays = 30)
    {
        setcookie($key, $value, time() + (86400 * $expireDays), "/"); // 30 days by default
    }

    public static function get($key)
    {
        return $_COOKIE[$key] ?? null;
    }

    public static function delete($key)
    {
        setcookie($key, "", time() - 3600, "/");
    }

    public static function exists($key)
    {
        return isset($_COOKIE[$key]);
    }
}
