<?php
namespace Bachor;

class Curl
{

    protected static $info;

    protected static $option = [
        CURLOPT_USERAGENT => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
        CURLOPT_RETURNTRANSFER => true
    ];

    public static function get($url, array $option = [])
    {
        $handle = curl_init($url);

        $option = $option + self::$option;

        curl_setopt_array($handle, $option);

        $response = curl_exec($handle);

        self::$info = curl_getinfo($handle);

        curl_close($handle);

        return $response;
    }

    public static function info($value = null)
    {
        if (empty(self::$info)) {
            return false;
        }

        return $value === null ? self::$info : self::$info[$value];
    }

    public static function post($url, array $data = [], $multipart = false, array $option = [])
    {
        $url = (string) $url;

        $handle = curl_init($url);

        $option = $option + self::$option;

        $option[CURLOPT_POST] = true;
        $option[CURLOPT_POSTFIELDS] = $multipart === true ? $data : http_build_query($data);

        curl_setopt_array($handle, $option);

        $response = curl_exec($handle);

        self::$info = curl_getinfo($handle);

        curl_close($handle);

        return $response;
    }
}
