<?php

namespace Dravencms\Seo\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Model\User\Repository\AclOperationRepository;
use Dravencms\Model\User\Repository\AclResourceRepository;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    /** @var MenuRepository */
    private $menuRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var AclOperationRepository */
    private $aclOperationRepository;

    /** @var AclResourceRepository */
    private $aclResourceRepository;

    /**
     * PostInstall constructor.
     * @param MenuRepository $menuRepository
     * @param EntityManager $entityManager
     * @param AclResourceRepository $aclResourceRepository
     * @param AclOperationRepository $aclOperationRepository
     */
    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager, AclResourceRepository $aclResourceRepository, AclOperationRepository $aclOperationRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->aclResourceRepository = $aclResourceRepository;
        $this->aclOperationRepository = $aclOperationRepository;
    }

    /**
     * @param IPackage $package
     * @throws \Exception
     */
    public function run(IPackage $package)
    {
        if (!$aclResource = $this->aclResourceRepository->getOneByName('seo')) {
            $aclResource = new AclResource('seo', 'Seo');

            $this->entityManager->persist($aclResource);
        }

        if (!$aclOperationEdit = $this->aclOperationRepository->getOneByName('edit')) {
            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of SEO');
            $this->entityManager->persist($aclOperationEdit);
        }

        if (!$aclOperationDelete = $this->aclOperationRepository->getOneByName('delete')) {
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of SEO');
            $this->entityManager->persist($aclOperationDelete);
        }

        if (!$aclOperationRobotsEdit = $this->aclOperationRepository->getOneByName('robotsEdit')) {
            $aclOperationRobotsEdit = new AclOperation($aclResource, 'robotsEdit', 'Allows editation of robots.txt');
            $this->entityManager->persist($aclOperationRobotsEdit);
        }

        if (!$aclOperationRobotsDelete = $this->aclOperationRepository->getOneByName('robotsDelete')) {
            $aclOperationRobotsDelete = new AclOperation($aclResource, 'robotsDelete', 'Allows deletion of robots.txt');
            $this->entityManager->persist($aclOperationRobotsDelete);
        }

        if (!$aclOperationTrackingEdit = $this->aclOperationRepository->getOneByName('trackingEdit')) {
            $aclOperationTrackingEdit = new AclOperation($aclResource, 'trackingEdit', 'Allows editation of tracking');
            $this->entityManager->persist($aclOperationTrackingEdit);
        }

        if (!$aclOperationTrackingDelete = $this->aclOperationRepository->getOneByName('trackingDelete')) {
            $aclOperationTrackingDelete = new AclOperation($aclResource, 'trackingDelete', 'Allows deletion of tracking');
            $this->entityManager->persist($aclOperationTrackingDelete);
        }

        if (!$adminMenuRoot = $this->menuRepository->getOneByName('SEO'))
        {
            $adminMenuRoot = new Menu('SEO', null, 'fa-binoculars', $aclOperationEdit);
            $this->entityManager->persist($adminMenuRoot);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Seo:Robots')) {
            $adminMenu = new Menu('Robots.txt', ':Admin:Seo:Robots', 'fa-fire', $aclOperationRobotsEdit);
            $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Seo:Tracking')) {
            $adminMenu = new Menu('Tracking', ':Admin:Seo:Tracking', 'fa-line-chart', $aclOperationTrackingEdit);
            $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Seo:TrackingService')) {
            $adminMenu = new Menu('Tracking services', ':Admin:Seo:TrackingService', 'fa-cog', $aclOperationEdit);
            $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);
        }

        $this->entityManager->flush();

    }
}