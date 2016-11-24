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

use Dravencms\Components\BaseGridFactory;
use App\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Seo\Repository\TrackingServiceRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

/**
 * Description of TrackingServiceGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TrackingServiceGrid extends Control
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var TrackingServiceRepository */
    private $trackingServiceRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RobotsGrid constructor.
     * @param TrackingServiceRepository $trackingServiceRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param LocaleRepository $localeRepository
     */
    public function __construct(TrackingServiceRepository $trackingServiceRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, LocaleRepository $localeRepository)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->trackingServiceRepository = $trackingServiceRepository;
        $this->localeRepository = $localeRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->trackingServiceRepository->getTrackingServiceQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setFilterText()
            ->setSuggestion();

        $countCol = function ($row) {
            return $row->position;
        };

        $grid->addColumnText('position', 'Position')
            ->setColumn($countCol)
            ->setCustomRender($countCol);
        $grid->getColumn('position')->cellPrototype->class[] = 'center';

        $countCol = function ($row) {
            return count($row->getTrackings());
        };

        $grid->addColumnText('users', 'Uses')
            ->setColumn($countCol)
            ->setCustomRender($countCol);
        $grid->getColumn('users')->cellPrototype->class[] = 'center';

        $grid->addColumnDate('updatedAt', 'Last edit', $this->localeRepository->getLocalizedDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('updatedAt')->cellPrototype->class[] = 'center';

        if ($this->presenter->isAllowed('seo', 'trackingEdit'))
        {
            $grid->addActionHref('edit', 'Edit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('seo', 'trackingDelete')) {
            $grid->addActionHref('delete', 'Delete', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function($row){
                    return (count($row->getTrackings()) > 0);
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($item) {
                    return ['Are you sure you want to delete %s ?', $item->getName()];
                });
        }

        $operations = ['delete' => 'Delete'];
        $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
            ->setConfirm('delete', 'Are you sure you want to delete %i items?');

        $grid->setExport();

        return $grid;
    }

    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $trackingServices = $this->trackingServiceRepository->getById($id);
        foreach ($trackingServices AS $trackingService)
        {
            $this->entityManager->remove($trackingService);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TrackingServiceGrid.latte');
        $template->render();
    }
}
