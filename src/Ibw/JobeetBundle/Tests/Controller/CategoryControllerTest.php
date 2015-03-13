<?php
namespace Ibw\JobeetBundle\Tests\Controller;

use Ibw\JobeetBundle\Tests\ControllerCase;

class CategoryControllerTest extends ControllerCase
{
    public function testShow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/category/design');
        $this->assertEquals('Ibw\JobeetBundle\Controller\CategoryController::showAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
    }
}






































