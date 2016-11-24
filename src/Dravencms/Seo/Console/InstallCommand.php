<?php

namespace Dravencms\Seo\Console;

use App\Model\Admin\Entities\Menu;
use App\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

class InstallCommand extends Command
{
    protected function configure()
    {
        $this->setName('dravencms:seo:install')
            ->setDescription('Installs dravencms module');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var MenuRepository $adminMenuRepository */
        $adminMenuRepository = $this->getHelper('container')->getByType('App\Model\Admin\Repository\MenuRepository');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getHelper('container')->getByType('Kdyby\Doctrine\EntityManager');

        try {

            $aclResource = new AclResource('seo', 'Seo');

            $entityManager->persist($aclResource);

            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of SEO');
            $entityManager->persist($aclOperationEdit);
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of SEO');
            $entityManager->persist($aclOperationDelete);

            $aclOperationRobotsEdit = new AclOperation($aclResource, 'robotsEdit', 'Allows editation of robots.txt');
            $entityManager->persist($aclOperationRobotsEdit);
            $aclOperationRobotsDelete = new AclOperation($aclResource, 'robotsDelete', 'Allows deletion of robots.txt');
            $entityManager->persist($aclOperationRobotsDelete);

            $aclOperationTrackingEdit = new AclOperation($aclResource, 'trackingEdit', 'Allows editation of tracking');
            $entityManager->persist($aclOperationTrackingEdit);
            $aclOperationTrackingDelete = new AclOperation($aclResource, 'trackingDelete', 'Allows deletion of tracking');
            $entityManager->persist($aclOperationTrackingDelete);

            $adminMenuRoot = new Menu('SEO', null, 'fa-binoculars', $aclOperationEdit);
            $entityManager->persist($adminMenuRoot);

            $adminMenu = new Menu('Robots.txt', ':Admin:Seo:Robots', 'fa-fire', $aclOperationRobotsEdit);
            $adminMenuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

            $adminMenu = new Menu('Tracking', ':Admin:Seo:Tracking', '	fa-line-chart', $aclOperationTrackingEdit);
            $adminMenuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

            $adminMenu = new Menu('Tracking services', ':Admin:Seo:TrackingService', '	fa-cog', $aclOperationEdit);
            $adminMenuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $adminMenuRoot);

            $entityManager->flush();

            $output->writeLn('Module installed successfully');
            return 0; // zero return code means everything is ok

        } catch (\Exception $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error
        }
    }
}