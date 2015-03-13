<?php
namespace Ibw\JobeetBundle\Tests\Entity\Repository;

use Ibw\JobeetBundle\Tests\ControllerCase;

class CategoryRepositoryTest extends ControllerCase
{
    public function testGetWithJobs()
    {
        $query = $this->em->createQuery('SELECT c FROM IbwJobeetBundle:Category c LEFT JOIN c.jobs j WHERE j.expires_at > :date');
        $query->setParameter('date', date('Y-m-d H:i:s', time()));
        $categories_db = $query->getResult();

        $categories_rep = $this->em->getRepository('IbwJobeetBundle:Category')->getWithJobs();
        // This test verifies if the number of categories having active jobs, returned
        // by the getWithJobs() function equals the number of categories having active jobs from database
        $this->assertEquals(count($categories_rep), count($categories_db));
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}