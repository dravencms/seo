<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Seo\TrackingServiceGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Model\Form\Entities\Form;
use Nette\Security\User;

/**
 * Description of TrackingServiceGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingServiceGrid extends BaseControl
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

    /** @var CurrentLocale */
    private $currentLocale;

    /** @var EntityManager */
    private $entityManager;
    
    /** @var User */
    private $user;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * TrackingServiceGrid constructor.
     * @param TrackingServiceRepository $trackingServiceRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     * @param CurrentLocaleResolver $currentLocaleResolver
     */
    public function __construct(
        TrackingServiceRepository $trackingServiceRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        User $user,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->trackingServiceRepository = $trackingServiceRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    /**
     * @param string $name
     * @return Grid
     */
    public function createComponentGrid(string $name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->trackingServiceRepository->getTrackingServiceQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setFilterText();

        $grid->addColumnNumber('position', 'Position')
            ->setAlign('center')
            ->setFilterRange();

        $countCol = function ($row) {
            return count($row->getTrackings());
        };

        $grid->addColumnText('users', 'Uses')
            ->setAlign('center')
            ->setRenderer($countCol);

        $grid->addColumnDateTime('updatedAt', 'Last edit')
            ->setFormat($this->currentLocale->getDateTimeFormat())
            ->setAlign('center')
            ->setSortable()
            ->setFilterDate();

        if ($this->presenter->isAllowed('seo', 'trackingEdit')) {

            $grid->addAction('edit', '', 'edit')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('seo', 'trackingDelete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirmation(new StringConfirmation('Do you really want to delete row %s?', 'name'));

            $grid->allowRowsAction('delete', function($item) {
                return !(count($item->getTrackings()) > 0);
            });

            $grid->addGroupAction('Smazat')->onSelect[] = [$this, 'handleDelete'];
        }

        return $grid;
    }
    
    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id): void
    {
        $trackingServices = $this->trackingServiceRepository->getById($id);
        foreach ($trackingServices AS $trackingService)
        {
            $this->entityManager->remove($trackingService);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingServiceGrid.latte');
        $template->render();
    }
}
