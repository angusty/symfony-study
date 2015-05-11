<?php
namespace Ibw\JobeetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ibw\JobeetBundle\Entity\Affiliate;

class LoadAffiliateData extends AbstractFixture
    implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $em)
    {
        // TODO: Implement load() method.
        $affiliate = new Affiliate();
        $affiliate->setUrl('http://sensio-lobs.com/');
        $affiliate->setEmail('8236138@qq.com');
        $affiliate->setToken('sensio-labs');
        $affiliate->setIsActive(true);
        $affiliate->addCategory($em->merge($this->getReference('category-programming')));

        $em->persist($affiliate);
        $affiliate = new Affiliate();
        $affiliate->setUrl('/');
        $affiliate->setEmail('yboker1982@gmail.com');
        $affiliate->setToken('symfony');
        $affiliate->setIsActive(false);
        $affiliate->addCategory($em->merge($this->getReference('category-programming')), $em->merge($this->getReference('category-design')));
        $em->persist($affiliate);
        $em->flush();
        $this->addReference('affiliate', $affiliate);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        // TODO: Implement getOrder() method.
        return 3;
    }

}