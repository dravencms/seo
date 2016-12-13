<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Dravencms\Model\Admin\Entities\Menu;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class AdminMenuFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $menu = $manager->getRepository(Menu::class);
        
        $adminMenuRoot = new Menu('SEO', null, 'fa-binoculars',  $this->getReference('user-acl-operation-seo-edit'));
        $manager->persist($adminMenuRoot);
 
        $adminMenu = new Menu('Robots.txt', ':Admin:Seo:Robots', 'fa-fire', $this->getReference('user-acl-operation-seo-robotsEdit'));
        $menu->persistAsLastChildOf($adminMenu, $adminMenuRoot);
    
        $adminMenu = new Menu('Tracking', ':Admin:Seo:Tracking', 'fa-line-chart', $this->getReference('user-acl-operation-seo-trackingEdit'));
        $menu->persistAsLastChildOf($adminMenu, $adminMenuRoot);


        $adminMenu = new Menu('Tracking services', ':Admin:Seo:TrackingService', 'fa-cog', $this->getReference('user-acl-operation-seo-trackingEdit'));
        $menu->persistAsLastChildOf($adminMenu, $adminMenuRoot);

        
        $manager->flush();
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getDependencies()
    {
        return ['Dravencms\Model\Seo\Fixtures\AclOperationFixtures'];
    }
}