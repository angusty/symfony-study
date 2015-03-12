<?php
namespace Ibw\JobeetBundle\Tests\Utils;

use Ibw\JobeetBundle\Utils\Jobeet;

class JobeetTest extends \PHPUnit_Framework_TestCase
{
    public function testSlugify()
    {
        $this->assertEquals('sensio', Jobeet::slugify('Sensio'));
        $this->assertEquals('sensio-labs', Jobeet::slugify('sensio labs'));
        $this->assertEquals('paris-france', Jobeet::slugify('paris, france'));
        $this->assertEquals('sensio', Jobeet::slugify(' sensio'));
        $this->assertEquals('sensio', Jobeet::slugify('sensio'));
        $this->assertEquals('中国-r人民', Jobeet::slugify('中国 r人民'));
        $this->assertEquals('n-a', Jobeet::slugify(''));
        $this->assertEquals('n-a', Jobeet::slugify(' -？? '));
        $this->assertEquals('développeur-web', Jobeet::slugify('Développeur Web'));
    }
}