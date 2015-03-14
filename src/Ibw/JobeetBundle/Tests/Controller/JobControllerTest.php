<?php

namespace Ibw\JobeetBundle\Tests\Controller;


use Ibw\JobeetBundle\Tests\ControllerCase;

class JobControllerTest extends ControllerCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals('Ibw\JobeetBundle\Controller\JobController::indexAction', $client->getRequest()->attributes->get('_controller'));
        //css选择器  .jobs td.position 包含有 contains() Expired的是过期的数据
        $this->assertTrue($crawler->filter('.jobs td.position:contains("Expired")')->count() == 0);

        $kernel = static::createKernel();
        $kernel->boot();
        $max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
        $this->assertTrue($crawler->filter('.category_programming tr')->count() <= $max_jobs_on_homepage);
    }
}
