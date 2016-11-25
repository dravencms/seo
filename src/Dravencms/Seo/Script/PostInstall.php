<?php

namespace Dravencms\Seo\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    private $menuRepository;
    private $entityManager;

    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
    }

    public function run(IPackage $package)
    {
        $aclResource = new AclResource('seo', 'Seo');

        $this->entityManager->persist($aclResource);

        $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of SEO');
        $this->entityManager->persist($aclOperationEdit);
        $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of SEO');
        $this->entityManager->persist($aclOperationDelete);

        $aclOperationRobotsEdit = new AclOperation($aclResource, 'robotsEdit', 'Allows editation of robots.txt');
        $this->entityManager->persist($aclOperationRobotsEdit);
        $aclOperationRobotsDelete = new AclOperation($aclResource, 'robotsDelete', 'Allows deletion of robots.txt');
        $this->entityManager->persist($aclOperationRobotsDelete);

        $aclOperationTrackingEdit = new AclOperation($aclResource, 'trackingEdit', 'Allows editation of tracking');
        $this->entityManager->persist($aclOperationTrackingEdit);
        $aclOperationTrackingDelete = new AclOperation($aclResource, 'trackingDelete', 'Allows deletion of tracking');
        $this->entityManager->persist($aclOperationTrackingDelete);

        $adminMenuRoot = new Menu('SEO', null, 'fa-binoculars', $aclOperationEdit);
        $this->entityManager->persist($adminMenuRoot);

        $adminMenu = new Menu('Robots.txt', ':Admin:Seo:Robots', 'fa-fire', $aclOperationRobotsEdit);
        $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

        $adminMenu = new Menu('Tracking', ':Admin:Seo:Tracking', '	fa-line-chart', $aclOperationTrackingEdit);
        $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

        $adminMenu = new Menu('Tracking services', ':Admin:Seo:TrackingService', '	fa-cog', $aclOperationEdit);
        $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

        $this->entityManager->flush();

    }
}