<?php
namespace Ibw\JobeetBundle\Twig;

class IbwJobeetExtension extends \Twig_Extension
{
    public  function  getName()
    {
        return 'ibw_jobeet_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('strtolower', array($this, 'strtolower'))
        );
    }

    public function strtolower($string)
    {
        return strtolower($string);
    }
}