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
        $this->assertTrue($crawler->filter('.jobs td.position:contains("Expired")')->count() == 0);
    }
}
