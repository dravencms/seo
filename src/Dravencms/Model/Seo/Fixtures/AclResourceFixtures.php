<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Fixtures;

use Dravencms\Model\User\Entities\AclResource;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class AclResourceFixtures extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $resources = [
            'seo' => 'Seo'
        ];
        foreach ($resources AS $resourceName => $resourceDescription)
        {
            $aclResource = new AclResource($resourceName, $resourceDescription);
            $manager->persist($aclResource);
            $this->addReference('user-acl-resource-'.$resourceName, $aclResource);
        }
        $manager->flush();
    }
}