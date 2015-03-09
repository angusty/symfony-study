<?php
namespace Ibw\JobeetBundle\Utils;

class Jobeet
{
    public static function slugify($text)
    {
        $text = preg_replace('/\W+/', '-', $text);
        $text = strtolower(trim($text, '-'));
        return $text;
    }
}