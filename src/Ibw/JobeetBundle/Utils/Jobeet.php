<?php
namespace Ibw\JobeetBundle\Utils;

class Jobeet
{
//    public static function slugify($text)
//    {
//        $text = preg_replace('/\W+/', '-', $text);
//        $text = strtolower(trim($text, '-'));
//        if (empty($text)) {
//            return 'n-a';
//        }
//        return $text;
//    }
    public static function slugify($text)
    {
        //replace non letter or digists by -
        $text = preg_replace('/[^\\pL\d]+/u', '-', $text);
        //trim
        $text = trim($text, '-');
        //transliterate
//        if (function_exists('iconv')) {
//            $text = iconv('utf-8', 'gb2312', $text);
//        }
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}