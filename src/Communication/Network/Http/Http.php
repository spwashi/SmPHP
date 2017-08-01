<?php
/**
 * User: Sam Washington
 * Date: 2/5/17
 * Time: 7:50 PM
 */

namespace Sm\Http;


class Http {
    public static $all_http_codes = [];
    public static function ___init() {
        if (file_exists(__DIR__ . '/http_codes.json')) {
            $str                    = file_get_contents(__DIR__ . '/http_codes.json');
            static::$all_http_codes = json_decode($str, true);
        }
    }
    public static function message($code) {
        $response = static::$all_http_codes[ $code ] ?? [];
        return $response['message'] ?? null;
    }
}

Http::___init();