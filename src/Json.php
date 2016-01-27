<?php
namespace Bachor;

class Json
{

    public static function decode($json, $assoc = false)
    {
        return json_decode($json, $assoc);
    }

    public static function encode($data)
    {
        return json_encode($data);
    }
}
