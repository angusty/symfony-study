<?php
namespace Ibw\Jobeet\Tests\Entity;

use Ibw\JobeetBundle\Tests\ControllerCase;
use Ibw\JobeetBundle\Utils\Jobeet;
use Ibw\JobeetBundle\Entity\Job;

class JobTest extends ControllerCase
{
    public function testGetCompanySlug()
    {
        $job = $this->em->createQuery('SELECT j FROM IbwJobeetBundle:Job j ')
            ->setMaxResults(1)
            ->getSingleResult();

        $this->assertEquals($job->getCompanySlug(), Jobeet::slugify($job->getCompany()));
    }

    public function testGetPositionSlug()
    {
        $job = $this->em->createQuery('SELECT j FROM IbwJobeetBundle:Job j ')
            ->setMaxResults(1)
            ->getSingleResult();

        $this->assertEquals($job->getPositionSlug(), Jobeet::slugify($job->getPosition()));
    }

    public function testGetLocationSlug()
    {
        $job = $this->em->createQuery('SELECT j FROM IbwJobeetBundle:Job j ')
            ->setMaxResults(1)
            ->getSingleResult();

        $this->assertEquals($job->getLocationSlug(), Jobeet::slugify($job->getLocation()));
    }

    public function testSetExpiresAtValue()
    {
        $job = new Job();
        $job->setExpiresAtValue();

        $this->assertEquals(time() + 86400 * 30, $job->getExpiresAt()->format('U'));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}