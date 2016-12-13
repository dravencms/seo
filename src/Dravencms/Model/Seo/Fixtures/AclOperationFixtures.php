<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Seo\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class AclOperationFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $operations = [
            'seo' => [
                'edit' => 'Umožnuje editaci SEO',
                'delete' => 'Umožnuje smazání SEO',
                'robotsEdit' => 'Umožnuje editaci robots',
                'robotsDelete' => 'Umožnuje smazání robots',
                'trackingEdit' => 'Umožnuje editaci robots',
                'trackingDelete' => 'Umožnuje smazání robots'
            ]
        ];

        foreach ($operations AS $resourceName => $operationList)
        {
            /** @var AclResource $aclResource */
            $aclResource = $this->getReference('user-acl-resource-'.$resourceName);
            foreach ($operationList AS $operationName => $operationDescription)
            {
                $aclOperation = new AclOperation($aclResource, $operationName, $operationDescription);
                //Allow all operations to administrator group
                $aclOperation->addGroup($this->getReference('user-group-administrator'));
                $manager->persist($aclOperation);
                $this->addReference('user-acl-operation-'.$resourceName.'-'.$operationName, $aclOperation);
            }
        }
        $manager->flush();
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getDependencies()
    {
        return ['Dravencms\Model\Seo\Fixtures\AclResourceFixtures', 'Dravencms\Model\User\Fixtures\GroupFixtures'];
    }
}